<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreContactMessageRequest;
use App\Http\Requests\Support\StoreOperatorEnquiryRequest;
use App\Http\Requests\Support\StoreSubscriberRequest;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\OperatorEnquiry;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    /**
     * Store visitor contact inquiry or facility discrepancy report.
     */
    public function submitContact(StoreContactMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();
        unset($validated['website']);

        $message = ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for contacting Parkly. Our support team will review your message and reply via email.',
            'id' => $message->id,
        ], 201);
    }

    /**
     * Store parking facility operator onboarding enquiry.
     */
    public function submitOperatorEnquiry(StoreOperatorEnquiryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        unset($validated['website']);

        $enquiry = OperatorEnquiry::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'status' => 'received',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Operator enquiry received. A Parkly representative will contact you to verify facility documentation.',
            'id' => $enquiry->id,
        ], 201);
    }

    /**
     * Subscribe email to platform updates.
     */
    public function subscribe(StoreSubscriberRequest $request): JsonResponse
    {
        $validated = $request->validated();
        unset($validated['website']);

        NewsletterSubscriber::create([
            'email' => strtolower(trim($validated['email'])),
            'ip_address' => $request->ip(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You are now subscribed to Parkly platform announcements and coverage updates.',
        ], 201);
    }
}
