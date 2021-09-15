<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'expires_at'
    ];

    /**
     * @param User $user
     * @return ActivationCode|\Illuminate\Database\Eloquent\Builder|Model
     */
    public static function createForUser(User $user)
    {
        $phone = $user->phone;
        return static::createWithPhone($phone);
    }

    /**
     * @param string $phone
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function createWithPhone(string $phone)
    {
        $code = make_random_numeric_token(6);
        $expiresAt = \Carbon\Carbon::now()->addMinutes(config('auth.activation_code_expire'));
        return static::query()->create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => $expiresAt
        ]);
    }

    public function send()
    {
        try {
            $sender = env('KAVENEGAR_SEND_NUMBER');
            $message = __('messages.success.successfully_registered', ['activationCode' => $this->code]);
            $receptor = [$this->phone];
            $result = \Kavenegar::Send($sender,$receptor,$message);
        } catch (\Exception $exception) {

        }

    }
}
