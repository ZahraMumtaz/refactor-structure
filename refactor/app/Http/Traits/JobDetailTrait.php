<?php

namespace app\Traits;

use DTApi\Models\Job;
use DTApi\Models\User;


trait JobDetailTrait
{

    public function getJobList($user){
        $jobDetail = [];
        $response  = [];
        $allJobs   = [];
        if ($cuser && $cuser->is('customer')) {
            $jobDetail = $this->getUserJobs($user);
        }elseif($cuser && $cuser->is('translator')){
            $jobDetail = $this->getTranslatorJob($user);
        }

        if(isset($jobDetail['jobs']) && !empty($jobDetail['jobs'])){
            $allJobs = $this->getNormalJobs($jobDetail['jobs']);
        }
        $response = ['emergencyJobs' => @$allJobs['emergencyJobs'], 'noramlJobs' => @$allJobs['noramlJobs'], 'cuser' => @$user, 'usertype' => @$jobDetail['usertype']];
        return $response;
    }

    public function getUserJob($customerUser){
        $jobs = $cuser->jobs()->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')->whereIn('status', ['pending', 'assigned', 'started'])->orderBy('due', 'asc')->get();
        $userType = 'customer';
        return  ['jobs' => $jobs, 'userType' =>  $userType ];
    }

    public function getTranslatorJob($cuser){
        $jobs = Job::getTranslatorJobs($cuser->id, 'new');
        $jobs = $jobs->pluck('jobs')->all();
        $usertype = 'translator';
        return  ['jobs' => $jobs, 'userType' =>  $userType ];
    }

    public function  getNormalJobs($jobs){
        $noramlJobs = [];
        foreach ($jobs as $jobitem) {
            if ($jobitem->immediate == 'yes') {
                $emergencyJobs[] = $jobitem;
            } else {
                $noramlJobs[] = $jobitem;
            }
        }
        $noramlJobs = collect($noramlJobs)->each(function ($item, $key) use ($user_id) {
            $item['usercheck'] = Job::checkParticularJob($user_id, $item);
        })->sortBy('due')->all();
        return ['emergencyJobs' => @$emergencyJobs, 'normalJobs' => @$noramlJobs];
    }

    public function getTranslatorJobHistory($cuser, $pagenum){
        $jobs      = Job::getTranslatorJobsHistoric($cuser->id, 'historic', $pagenum);
        $totaljobs = $jobs_ids->total();
        $numpages  = ceil($totaljobs / 15);
        $usertype  = 'translator';
        return ['jobs' => @$jobs, 'usertype' => $userType, 'numpages' => $numpages];
    }

    


}

