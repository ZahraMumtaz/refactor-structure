<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{
    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct()
    { 

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request, BookingRepository $bookingRepository)
    {
        if($user_id = $request->get('user_id')) {

            $response = $bookingRepository>getUsersJobs($user_id);

        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $bookingRepository->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show(BookingRepository $bookingRepository,$id)
    {
        $job = $bookingRepository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(BookingRepository $bookingRepository,BookingRequest $request)
    {
        $data = $request->all();

        $response = $bookingRepository->store($request->__authenticatedUser, $data);

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update(BookingRepository $bookingRepository,$id, BookingRequests $request)
    {
        $data = $request->all();
        $cuser = $request->__authenticatedUser;
        $response = $bookingRepository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $adminSenderEmail = config('app.adminemail');
        $data = $request->all();

        $response = $bookingRepository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $bookingRepository->getUsersJobsHistory($user_id, $request);
            return response($response);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $bookingRepository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $bookingRepository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $bookingRepository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();

        $response = $bookingRepository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();

        $response = $bookingRepository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $bookingRepository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();

        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }
        
        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $admincomment = $data['admincomment'];
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {

            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));

        }

        return response('Record updated!');
    }

    public function reopen(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();
        $response = $bookingRepository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request,BookingRepository $bookingRepository)
    {
        $data = $request->all();
        $job = $bookingRepository->find($data['jobid']);
        $job_data = $bookingRepository->jobToData($job);
        $bookingRepository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request, BookingRepository $bookingRepository)
    {
        $data = $request->all();
        $job = $bookingRepository->find($data['jobid']);
        $job_data = $bookingRepository->jobToData($job);

        try {
            $bookingRepository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
