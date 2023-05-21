<?php

use Tests\TestCase;
use App\Repositories\BookingRepository;
use Mockery;

class BookingController extends TestCase{

    public function testIndexMethod(){
        $user = 'testUsers';
        $bookingRepositoryMock = Mockery::mock(BookingRepository::class);
        $requestMock = Mockery::mock(MyFormRequest::class);
        $bookingRepositoryMock->shouldReceive('getUsersJobs')->with($user)->andReturn(['jobs' => [],'usertype' => '']);
        $bookingController = new BookingController();
        $response = $bookingController->index($requestMock,$bookingRepositoryMock);
        $this->assertJson($response->getContent());
    }

    public function testShowFunction(){
        $bookingRepositoryMock = Mockery::mock(BookingRepository::class);
        $bookingController = new BookingController();
        $bookingRepositoryMock->shouldReceive('with')->with('translatorJobRel.user')->andReturnSelf();
        $bookingRepositoryMock->shouldReceive('find')->with('2')->andReturn(['id' => 1, 'name' => 'testuser']);
        $response = $bookingController->show($bookingRepositoryMock,'testId');
        $assertEquals(['id' => 1, 'name' => 'testuser'], $response);
    }   

}