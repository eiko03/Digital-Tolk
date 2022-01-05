<?php

namespace DTApi\Http\Controllers;

use App\Http\Requests\AcceptJobRequest;
use App\Http\Requests\cancelJobRequest;
use App\Http\Requests\DistanceFeedRequest;
use App\Http\Requests\EndJobRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\StoreJobEmailRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
enum RequestGetter:string {
    case user_id="user_id";
    case job_id="job_id";
    case page_id="page_id";

}
class BookingController extends Controller
{
    protected array $storeGetters = [
        'from_language_id',
        'immediate',
        'immediate',
        'due_date',
        'due',
        'due_time',
        'duration',
        'customer_phone_type',
        'customer_physical_type',
        'gender',
        'certified',
        'job_for',
        'job_type',
        'b_created_at',
        'will_expire_at',
        'by_admin',
        'customer_town'
    ];
    protected array $updateGetters = [
        'cancel_at',
        'completed_at',
        'log_data',
        'due',
        'dateChanged',
        'from_language_id',
    ];
    protected array $storeJobGetters = [
        'user_type',
        'user_email',

    ];
    protected array $cancelJobGetters = [
        'data',
        'notification_type',

    ];
    protected array $endJobGetters = [
        'job_id',
        'user_id',

    ];
    protected array $distanceFeedGetters = [
        'job_id',
        'distance',
        'time',
        'jobid',
        'session_time',
        'flagged',
        'admincomment',
        'manually_handled',
        'by_admin',
    ];


    /**
     * @var BookingRepository
     */

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */

    /**
     * PHP 8.1 Syntax, Constructor Promotion
     */

    public function __construct(
        protected BookingRepository $repository
    )
    {
        $this->middleware('Authenticate');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(IndexRequest $request)
    {
        if($user_id = $request->get(RequestGetter::user_id))
            return response( $this->repository->getUsersJobs($user_id));


        elseif(
            in_array(
                $request->__authenticatedUser->user_type ,
                [env('ADMIN_ROLE_ID') , env('SUPERADMIN_ROLE_ID')]
            )
        )
            return response( $this->repository->getAll($request));



    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return response(
            $this->repository->with('translatorJobRel.user')
                ->find($id)
        );

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {

        return response(
            $this->repository->store(
                $request->__authenticatedUser, $request->get($this->storeGetters)
            )
        );

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, UpdateRequest $request)
    {
        return response( $this->repository->updateJob(
            $id,
            $request->get($this->updateGetters),
            $request->__authenticatedUser
            )
        );


    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(StoreJobEmailRequest $request)
    {

        return response(
            $this->repository->storeJobEmail(
                $request->get($this->storeJobGetters)
            )
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(PageRequest $request)
    {
        if($user_id = $request->get(RequestGetter::user_id)) {

            return response(
                $this->repository->getUsersJobsHistory(
                    $user_id,
                    $request->get(RequestGetter::page_id)
                )
            );

        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(AcceptJobRequest $request)
    {
        return response(
            $this->repository->acceptJob(
                $request->get(RequestGetter::job_id),
                $request->__authenticatedUser
            )
        );
    }

    public function acceptJobWithId(AcceptJobRequest $request)
    {

        return response(
            $this->repository->acceptJobWithId(
                $request->get(RequestGetter::job_id),
                $request->__authenticatedUser
            )
        );


    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(cancelJobRequest $request)
    {
        return response(
                $this->repository->cancelJobAjax(
                    $request->get($this->cancelJobGetters),
                    $request->__authenticatedUser
            )
        );

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(EndJobRequest $request)
    {

        return response(
            $this->repository->endJob(
                $request->get($this->endJobGetters)
            )
        );

    }

    public function customerNotCall(AcceptJobRequest $request)
    {
        return response(
            $this->repository->customerNotCall(
                $request->get(RequestGetter::job_id)
            )
        );

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {

        return response(
            $this->repository->getPotentialJobs(
                $request->__authenticatedUser
            )
        );
    }

    public function distanceFeed(DistanceFeedRequest $request)
    {
        return response(
            $this->repository->distanceFeed(
                $request->get($this->distanceFeedGetters)
            )
        );
    }

    public function reopen(EndJobRequest $request)
    {
        return response(
            $this->repository->reopen(
                $request->get($this->endJobGetters)
            )
        );

    }

    public function resendNotifications(PageRequest $request)
    {

        $job = $this->repository->find($request->get(RequestGetter::job_id));
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(PageRequest $request)
    {
        try {
            $this->repository->sendSMSNotificationToTranslator(
                $this->repository->find($request->get(RequestGetter::job_id))
            );
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()],500);
        }
    }

}
