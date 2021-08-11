<?php

namespace App\Repositories;


use App\Exceptions\ContentWasNotFountException;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\User;

class TicketRepository extends BaseRepository
{

    /**
     * @param StoreTicketRequest $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createTicket(StoreTicketRequest $request)
    {
        $user = $request->user();
        $inputs = $request->validated();
        $inputs['user_id'] = $user->id;
        if ($request->exists('image')) {
            $inputs['image'] = $this->saveFileFromRequest($request, 'image', 'tickets');
        }
        return Ticket::query()->create($inputs);
    }

    /**
     * @param User $user
     * @param string $state
     * @param int $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws ContentWasNotFountException
     */
    public function getTicketsByState(User $user, string $state, int $paginate = 10)
    {
        if (! in_array($state, ['open', 'close', 'all'])) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $query = Ticket::query()->where('user_id', $user->id);
        if ($state == 'open') {
            $query = $query->where('is_open', true);
        } else if ($state == 'close') {
            $query = $query->where('is_open', false);
        }

        $query = $query->orderBy('created_at', 'desc');
        if ($paginate) {
            return $query->paginate($paginate);
        }
        return $query->get();
    }
}