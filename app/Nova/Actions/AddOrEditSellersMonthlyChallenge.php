<?php

namespace App\Nova\Actions;

use App\Enums\GoalType;
use App\Enums\UserType;
use App\Models\MonthlyChallenge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Morilog\Jalali\Jalalian;

class AddOrEditSellersMonthlyChallenge extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('nova.add_edit_sellers_challenge');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $year = $fields->year;
        $month = $fields->month;
        $challenge = MonthlyChallenge::query()
            ->where('user_type', UserType::SELLER)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
        if (!$challenge) {
            $challenge = new MonthlyChallenge();
        }
        $startsAt = (new Jalalian($year, $month, 1))->toCarbon();
        $nextMonth = (new Jalalian($year, $month, 1))->addMonths(1);
        $endsAt = (new Jalalian($nextMonth->getYear(), $nextMonth->getMonth(), 1))->toCarbon();
        $description = $fields->challenge_name;
        $prize = $fields->challenge_prize;
        $goalAmount = $fields->goal_orders_weight_sum;
        $now = \Carbon\Carbon::now();
        $isActive = $now >= $startsAt && $now <= $endsAt;
        $challenge->setAttribute('user_type', UserType::SELLER)
            ->setAttribute('year', $year)
            ->setAttribute('month', $month)
            ->setAttribute('description', $description)
            ->setAttribute('prize', $prize)
            ->setAttribute('goal_type', GoalType::WEIGHT)
            ->setAttribute('goal_amount', $goalAmount)
            ->setAttribute('starts_at', $startsAt)
            ->setAttribute('ends_at', $endsAt)
            ->setAttribute('is_active', $isActive)
            ->save();
        Action::message(__('messages.success.successful_operation'));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        $months = $this->getMonths();
        $years = $this->getYears();
        $allYears = array_keys($years);

        return [
            Select::make(__('nova.year'), 'year')
                ->options($years)
                ->displayUsingLabels()
                ->default($years[$allYears[0]])
                ->rules('required'),

            Select::make(__('nova.month'), 'month')
                ->options($months)
                ->displayUsingLabels()
                ->rules('required'),

            Text::make(__('nova.challenge_name'), 'challenge_name')
                ->rules('required'),

            Text::make(__('nova.challenge_prize'), 'challenge_prize')
                ->rules('required'),

            Number::make(__('nova.goal_orders_weight_sum'), 'goal_orders_weight_sum')
                ->step(0.1)
                ->rules('required', 'min:1'),
        ];
    }

    /**
     * @return array
     */
    protected function getMonths()
    {
        return [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
    }

    /**
     * @return array
     */
    protected function getYears()
    {
        $year = Jalalian::now()->getYear();
        $nextYear = $year + 1;
        return [
            $year => $year,
            $nextYear => $nextYear,
        ];

    }
}
