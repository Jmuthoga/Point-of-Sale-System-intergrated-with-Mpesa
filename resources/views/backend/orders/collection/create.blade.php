@extends('backend.master')

@section('title', 'Collection')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('backend.admin.due.collection', $order->id) }}" method="POST" class="accountForm">
      @csrf
      <div class="row">

        <!-- Customer Info -->
        <div class="mb-3 col-md-3">
          <label class="form-label">Name</label>
          <p>{{ $order->customer->name }}</p>
        </div>
        <div class="mb-3 col-md-3">
          <label class="form-label">Order</label>
          <p>#{{ $order->id }}</p>
        </div>
        <div class="mb-3 col-md-3">
          <label class="form-label">Total</label>
          <p>{{ number_format($order->total, 2) }}</p>
        </div>
        <div class="mb-3 col-md-3">
          <label class="form-label">Due</label>
          <p>{{ number_format($order->due, 2) }}</p>
        </div>

        <!-- Amount -->
        <div class="mb-3 col-md-6">
          <label class="form-label">Collection Amount <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="amount" value="{{ $order->due }}" min="1" max="{{ $order->due }}" required>
        </div>

        <!-- Payment Method -->
        <div class="mb-3 col-md-6">
          <label class="form-label">Payment Method <span class="text-danger">*</span></label>
          <select class="form-control" name="paid_by" id="paid_by" required>
            <option value="">-- Select Payment Method --</option>
            <option value="cash">Cash</option>
            <option value="mpesa">MPESA</option>
            <!-- <option value="bank">Bank</option> -->
          </select>
        </div>

        <!-- MPESA Details (Dynamic Display) -->
        <div class="mb-3 col-md-6 phone-number-wrapper" style="display: none;">
          <label class="form-label">Customer Phone Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="2547XXXXXXXX">
          <div class="invalid-feedback d-none" id="phoneError">Invalid phone number. Format should be 2547XXXXXXXX.</div>
        </div>

        <div class="mb-3 col-md-6 stk-wrapper" style="display: none;">
          <label class="form-label d-block">&nbsp;</label>
          <button type="button" class="btn btn-primary" id="stkPushBtn">Send STK Push</button>
        </div>

        <!-- Response Message -->
        <div class="col-md-12 mt-3">
          <div id="responseMsg" class="alert d-none"></div>
        </div>

        <!-- Submit -->
        <div class="col-md-6 mt-4">
          <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('.accountForm');
  const paidBy = document.getElementById('paid_by');
  const phoneWrapper = document.querySelector('.phone-number-wrapper');
  const stkWrapper = document.querySelector('.stk-wrapper');
  const phoneInput = document.getElementById('phone_number');
  const amountInput = document.querySelector('input[name="amount"]');
  const stkBtn = document.getElementById('stkPushBtn');
  const submitBtn = document.getElementById('submitBtn');
  const phoneError = document.getElementById('phoneError');
  const responseMsg = document.getElementById('responseMsg');

  // Toggle MPESA fields
  paidBy.addEventListener('change', function () {
    if (this.value === 'mpesa') {
      phoneWrapper.style.display = 'block';
      stkWrapper.style.display = 'block';
      phoneInput.setAttribute('required', 'required');
      submitBtn.disabled = true;
    } else {
      phoneWrapper.style.display = 'none';
      stkWrapper.style.display = 'none';
      phoneInput.removeAttribute('required');
      submitBtn.disabled = false;
    }
  });

  // STK Push Logic
  stkBtn.addEventListener('click', function () {
    const phone = phoneInput.value.trim();
    const amount = parseFloat(amountInput.value);

    // Validate phone format
    if (!/^2547\d{8}$/.test(phone)) {
      phoneInput.classList.add('is-invalid');
      phoneError.classList.remove('d-none');
      return;
    } else {
      phoneInput.classList.remove('is-invalid');
      phoneError.classList.add('d-none');
    }

    stkBtn.disabled = true;
    stkBtn.innerText = 'Sending...';
    responseMsg.classList.add('d-none');

    fetch("{{ url('/api/mpesa/stk-push') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        phone: phone,
        amount: amount,
        order_id: {{ $order->id }}
      })
    })
    .then(res => res.json())
    .then(data => {
      responseMsg.classList.remove('d-none');
      responseMsg.classList.add(data.success ? 'alert-success' : 'alert-danger');
      responseMsg.textContent = data.message || (data.success ? 'STK Push sent.' : 'STK Push failed.');

      if (data.success) {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Confirm & Submit';
      }
    })
    .catch(() => {
      responseMsg.classList.remove('d-none', 'alert-success');
      responseMsg.classList.add('alert-danger');
      responseMsg.textContent = 'Network error. Try again.';

      // Hide the error after 3 seconds
      setTimeout(() => {
        responseMsg.classList.add('d-none');
      }, 5000);
    })
    .finally(() => {
      stkBtn.disabled = false;
      stkBtn.innerText = 'Send STK Push';
    });
  });
});
</script>
@endpush
