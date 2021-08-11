<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ContentWasNotFountException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use ResponseMaker;

    /**
     * @param StoreTicketRequest $request
     * @param TicketRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreTicketRequest $request, TicketRepository $repository)
    {
        return $this->success($repository->createTicket($request));
    }

    /**
     * @param Request $request
     * @param string $state
     * @param TicketRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\ContentWasNotFountException
     */
    public function getTickets(Request $request, string $state, TicketRepository $repository)
    {
        $user = $request->user();
        return $this->success($repository->getTicketsByState($user, $state));
    }

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function closeTicket(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ($ticket->user_id == $user->id) {
            $ticket->is_open = false;
            $ticket->save();
            return $this->success($ticket);
        }
        throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
    }

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function getTicketById(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ($ticket->user_id == $user->id) {
            return $this->success($ticket);
        }
        throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
    }
}
