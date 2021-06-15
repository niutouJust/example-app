<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendMail extends Controller
{
    /**
     * Ship the given order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
//        $order = Order::findOrFail($request->order_id);

        // Ship the order...

        $order = array('order_id'=> '123');

        $content = $this->test_mailable_content();

        Mail::to($request->user())->send($content);
    }


    public function test_mailable_content()
    {
        $user = User::factory()->create();

        $mailable = new OrderShipped();

        $mailable->assertSeeInHtml($user->email);
        $mailable->assertSeeInHtml('Invoice Paid');

        $mailable->assertSeeInText($user->email);
        $mailable->assertSeeInText('Invoice Paid');
    }
}
