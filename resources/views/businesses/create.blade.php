@extends('layouts.app')

@section('content')
    <h1>Add Business</h1>

    <form method="POST" action="/businesses">
        @csrf

        <p>
            <label>Business or Agency Name</label><br>
            <input name="name" placeholder="Business or Agency Name" required>
        </p>
        <p>
            <label>Lead Type</label><br>
            <select name="lead_type" required>
                <option value="business">Direct Business</option>
                <option value="pr_agency">PR Agency</option>
            </select>
        </p>
        <p><input name="category" placeholder="Category"></p>
        <p><input name="website" placeholder="Website"></p>
        <p><input name="instagram" placeholder="Instagram"></p>
        <p><input name="email" placeholder="Email"></p>
        <p><input name="contact_name" placeholder="Contact Name"></p>
        <p><input name="pr_contact_role" placeholder="PR Contact Role"></p>
        <p><textarea name="agency_specialties" placeholder="Agency Specialties"></textarea></p>
        <p><textarea name="client_types" placeholder="Client Types"></textarea></p>
        <p><input name="roster_status" placeholder="Roster Status"></p>
        <p>
            <label>Media Kit Sent Date</label><br>
            <input type="date" name="media_kit_sent_at">
        </p>
        <p><input name="phone" placeholder="Phone"></p>
        <p><input name="address" placeholder="Address"></p>
        <p><input name="city" placeholder="City"></p>
        <p><input name="state" placeholder="State"></p>
        <p><input name="contact_source" placeholder="Contact Source"></p>
        <p><input type="number" step="0.01" name="collab_value" placeholder="Collab Value"></p>
        <p><textarea name="deliverables" placeholder="Deliverables"></textarea></p>
        <p><input type="number" step="0.01" name="compensation" placeholder="Compensation"></p>
        <p>
            <label>Booking Date</label><br>
            <input type="date" name="booking_date">
        </p>
        <p>
            <label>Posting Date</label><br>
            <input type="date" name="posting_date">
        </p>
        <p><input name="posted_url" placeholder="Posted URL"></p>
        <p>
            <label>Payment Status</label><br>
            <select name="payment_status">
                <option value="">Select Payment Status</option>
                <option value="Pending">Pending</option>
                <option value="Partially Paid">Partially Paid</option>
                <option value="Paid">Paid</option>
            </select>
        </p>
        <p><textarea name="notes" placeholder="Notes"></textarea></p>

        <button type="submit">Save Business</button>
    </form>

    <p><a href="/businesses">Back</a></p>
@endsection
