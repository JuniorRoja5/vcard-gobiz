<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\BusinessCard;
use App\EmailTemplate;
use App\BookedAppointment;
use App\CardAppointmentTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class BookAppointmentController extends Controller
{
    // Book appointment
    public function bookAppointment(Request $request)
    {
        // Validate Recaptcha
        if (env('RECAPTCHA_ENABLE') == 'on') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'date' => 'required|date',
                'time_slot' => 'required|string',
                'price' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => trans('Please fill out all the fields.')]);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'date' => 'required|date',
                'time_slot' => 'required|string',
                'price' => 'required'
            ]);
        }

        // Save the appointment
        $bookAppointment = new BookedAppointment();
        $bookAppointment->booked_appointment_id = uniqid();
        $bookAppointment->card_id = $request->card;
        $bookAppointment->name = $request->name;
        $bookAppointment->email = $request->email;
        $bookAppointment->phone = $request->phone;
        $bookAppointment->notes = $request->notes;
        $bookAppointment->booking_date = $request->date;
        $bookAppointment->booking_time = $request->time_slot;
        $bookAppointment->total_price = $request->price;
        $bookAppointment->save();

        // Get vcard owner email
        $vcardOwner = BusinessCard::where('card_id', $request->card)->first();
        $businessName = $vcardOwner->title;
        $businessVcardUrl = url($vcardOwner->card_url);

        // Check enquiry email is empty
        if ($vcardOwner == null) {
            $vcardOwnerEmail = "";
        } else {
            $vcardOwnerEmail = $vcardOwner->enquiry_email;
        }

        // Get appointment pending email template content
        $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675196')->first();

        // Booking mail sent to customer
        if ($emailTemplateDetails->is_enabled == 1) {
            $details = [
                'status' => "Pending",
                'emailSubject' => $emailTemplateDetails->email_template_subject,
                'emailContent' => $emailTemplateDetails->email_template_content,
                'appointmentDate' => $request->date,
                'appointmentTime' => $request->time_slot,
                'vcardName' => $businessName,
                'vcardUrl' => $businessVcardUrl,
                'googleCalendarUrl' => "",
                'customerName' => "",
                'cardId' => $request->card
            ];
        }

        $vcardOwnerMailTemplateDetails = EmailTemplate::where('email_template_id', '584922675201')->first();

        // Booking mail sent to vcard owner
        $ownerdDetails = [
            'status' => "",
            'emailSubject' => $vcardOwnerMailTemplateDetails->email_template_subject,
            'emailContent' => $vcardOwnerMailTemplateDetails->email_template_content,
            'appointmentDate' => $request->date,
            'appointmentTime' => $request->time_slot,
            'vcardName' => $businessName,
            'vcardUrl' => $businessVcardUrl,
            'googleCalendarUrl' => "",
            'customerName' => $request->name,
            'cardId' => $request->card
        ];

        try {
            Mail::to($request->email)->send(new \App\Mail\AppointmentMail($details));
            Mail::to($vcardOwnerEmail)->send(new \App\Mail\AppointmentMail($ownerdDetails));
            // Mail::to($vcardOwnerEmail)->send(new \App\Mail\VcardOwnerAppointmentMail($ownerdDetails));
        } catch (\Exception $e) {
        }

        return response()->json(['success' => true, 'message' => trans('Appointment booked successfully!')]);
    }

    // Get day wise available time slots
    public function getAvailableTimeSlots(Request $request)
    {
        // Parse the input day into a Carbon date object
        $cardId = $request->card;

        // Add one day
        $addOneDay = $request->choose_date;

        // Format the new date
        $Date = Carbon::parse($addOneDay)->addDay(); // Add one day
        $choosedDate = $Date->format('Y-m-d'); // Format the new date
        $day = Carbon::parse($request->day);

        // Retrieve already booked appointments for the specified card and date
        $bookedAppointments = BookedAppointment::where('card_id', $cardId)
            ->whereDate('booking_date', $choosedDate) // Use whereDate to match the date only
            ->whereIn('booking_status', [0, 1]) // Exclude booked and confirmed appointments
            ->pluck('booking_time'); // Pluck the booking times directly

        // Convert booked appointments to an array
        $excludedTimeSlots = $bookedAppointments->toArray(); // Now $excludedTimeSlots contains booked times

        // Retrieve available time slots, excluding already booked times
        $availableTimeSlots = CardAppointmentTime::where('card_id', $cardId)
            ->where('day', strtolower($day->format('l'))) // Get the day name (e.g., 'friday')
            ->pluck('time_slots');

        // Check if availableTimeSlots is not empty before accessing index 0
        if ($availableTimeSlots->isEmpty()) {
            return response()->json(['success' => false, 'message' => __('No available time slots for this day.')]);
        }

        // Decode the available time slots JSON string into an array
        $availableTimeSlots = json_decode($availableTimeSlots->first(), true) ?? [];

        // Ensure excluded time slots exist
        $excludedTimeSlots = $excludedTimeSlots ?? [];

        // Use array_diff to find available slots that are not in excluded slots
        $availableTimeSlots = array_diff($availableTimeSlots, $excludedTimeSlots);

        // Re-index the array if needed
        $availableTimeSlots = array_values($availableTimeSlots);

        // Optionally, if you need to encode it back to JSON
        $availableTimeSlotsJson = json_encode($availableTimeSlots);

        // Get price safely
        $price = optional(CardAppointmentTime::where('card_id', $cardId)->first());

        return response()->json(['success' => true, 'available_time_slots' => $availableTimeSlotsJson, 'price' => $price->price]);
    }
}
