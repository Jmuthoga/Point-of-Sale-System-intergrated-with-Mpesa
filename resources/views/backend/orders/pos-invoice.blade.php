@extends('backend.master')
@section('title', 'Receipt_'.$order->id)
@section('content')

<div class="card">
  <!-- Main content -->
  <div class="receipt-container mt-0" id="printable-section" style="max-width: {{ $maxWidth}}; font-size: 20px; font-family: 'Courier New', Courier, monospace;">
    <div class="text-center">
      @if(readConfig('is_show_logo_invoice'))
     <img src="{{ assetImage(readconfig('site_logo')) }}" height="90" width="160" alt="Logo">
      @endif
      @if(readConfig('is_show_site_invoice'))
      <h3>{{ readConfig('site_name') }}</h3>
      @endif
      @if(readConfig('is_show_address_invoice')){{ readConfig('contact_address') }}<br>@endif
      @if(readConfig('is_show_phone_invoice')){{ readConfig('contact_phone') }}<br>@endif
      @if(readConfig('is_show_email_invoice')){{ readConfig('contact_email') }}<br>@endif
    </div>
    <div class="text-center">
      {{ 'Served by: '.auth()->user()->name }}<br>
      {{ 'Order No: #'.$order->id }}<br>
    </div>

    <hr>
    <div class="row justify-content-between mx-auto">
      <div class="text-center">
        @if(readConfig('is_show_customer_invoice'))
        <address style="display: flex; flex-direction: column; align-items: flex-start;">
         <span>Name: {{ $order->customer->name ?? 'N/A' }}</span>
         <span>Address: {{ $order->customer->address ?? 'N/A' }}</span>
         <span>Phone: {{ $order->customer->phone ?? 'N/A' }}</span>
        </address>

        @endif
      </div>
      <div class="text-center">
        <address class="text-center">
          <p>{{ date('d-M-Y') }} &nbsp; {{ date('h:i:s A') }}</p>
        </address>

      </div>
    </div>
    <hr>
<table style="width: 100%; border-collapse: collapse;">
  <thead class="product-header">
    <tr>
      <th style="text-align: left; padding: 4px;">Product</th>
      <th style="text-align: right; padding: 4px;">&nbsp;</th> <!-- empty header for spacing -->
      <th style="text-align: right; padding: 4px;">Qty</th> 
      <th style="text-align: right; padding: 4px;">&nbsp;</th> <!-- empty header for spacing -->
      <th style="text-align: right; padding: 4px;">Price</th> 
      <th style="text-align: right; padding: 4px;">Total ({{ currency()->symbol }})</th>
    </tr>
  </thead>
      <tbody>
<tbody>
@foreach ($order->products as $item)
<tr class="product-row">
  <td style="padding: 4px 0;">{{ $item->product->name }}</td>
  <td></td>
  <td class="text-right" style="padding: 4px 0;">{{ $item->quantity }}</td> 
  <td></td>
  <td class="text-right" style="padding: 4px 0;">{{ $item->discounted_price }}</td>
  <td class="text-right" style="padding: 4px 0;">{{ $item->total }}</td>
</tr>
@endforeach
</tbody>


      </tbody>
    </table>
    <hr>
    <div class="summary">
      <table style="width: 100%;">
        <tr>
          <td>Subtotal:</td>
          <td class="text-right">{{number_format($order->sub_total, 2) }}</td>
        </tr>
        <tr>
          <td>Discount:</td>
          <td class="text-right">{{number_format($order->discount, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Total ({{ currency()->symbol}}):</strong></td>
          <td class="text-right"><strong>{{number_format($order->total, 2) }}</strong></td>
        </tr>
        <tr>
          <td>Paid:</td>
          <td class="text-right">{{number_format($order->paid, 2) }}</td>
        </tr>
        <tr>
          <td>Due:</td>
          <td class="text-right">{{number_format($order->due, 2) }}</td>
        </tr>
      </table>
    </div>
    <hr>
<div class="text-center" id="jm-footer">
  <p class="text-muted" style="font-size: 20px;">@if(readConfig('is_show_note_invoice')){{ readConfig('note_to_customer_invoice') }}@endif</p>
  
  <div id="qr_code_container" style="display: flex; justify-content: center; margin: 10px 0;">
    <div id="qr_code"></div>
  </div>
  
  <p class="text-muted" style="font-size: 20px; font-weight: bold;">
    This pos system is developed by JM Innovatech Solutions<br>
    info@jminnovatechsolution.co.ke<br>
    0791446968
  </p>
</div>

  </div>

  <!-- Print Button -->
  <div class="text-center mt-3 no-print pb-3">
    <button type="button" onclick="window.print()" class="btn bg-gradient-primary text-white"><i class="fas fa-print"></i> Print</button>
  </div>
</div>
@endsection

@push('style')
<style>
.receipt-container {
  width: 100%;
  max-width: 80mm;
  margin-left: 5px;
  margin-right: auto;
  padding-left: 2px;
  font-family: 'Courier New', Courier, monospace;
  font-size: 12px;
  line-height: 1.3;
  font-weight: bold; 
  color: #000;
}


  hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 5px 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    padding: 2px 0;
    text-align: left;
  }
  
   th {
    font-weight: bold;
  }

  .text-right {
    text-align: right !important;
  }
    .text-center {
    text-align: center !important;
  }
  
    .text-left {
    text-align: left !important;
  }
    .summary td {
    padding: 2px 0;
  }
  
  .product-row {
  border-bottom: 1px dotted #000;
}

.product-row:last-child {
  border-bottom: none;
}
.product-header tr {
  border-bottom: 1px dotted #000;
}



  /* Print-specific Styles */
  @media print {
    * {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color: #000 !important; /* Make all text black for clarity */
    }

    @page {
      size: auto;
      margin: 0;
    }

    html, body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100%;
      background: #fff !important;
      font-size: 20px;
      font-family: 'Courier New', Courier, monospace;
      color: #000;
    }

    .receipt-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100% !important;
      height: 100% !important;
      margin: 0 auto;
      padding: 8px;
      font-size: 20px;
      box-sizing: border-box;
      color: #000;
        margin-left: -10px;
      background-color: #fff !important;
    }


    footer {
      display: none !important;
    }

    #qr_code img {
      filter: grayscale(100%) contrast(150%);
    }

    .text-muted {
      color: #000 !important;
    }

    img {
      max-width: 100%;
      height: auto;
    }


tbody tr:last-child {
  border-bottom: none;
}

tbody td {
  padding-top: 4px;
  padding-bottom: 4px;
}

  }
</style>
@endpush

@push('script')
<!-- Include QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  window.print();
</script>

<script>
window.onload = function () {
    // Generate QR Code
    let qrText = "@if(readConfig('is_show_note_invoice')){{ readConfig('note_to_customer_invoice') }}@endif";
    let qrDiv = document.getElementById("qr_code");

    if (qrDiv) {
        new QRCode(qrDiv, {
            text: qrText,
            width: 150,
            height: 100
        });
    }

    // Clone the receipt for second print
    const original = document.getElementById("printable-section");
    if (original) {
        const parent = original.parentNode;
         const clone = original.cloneNode(true);

        // Fix QR code clone issue by regenerating it
        const cloneQrDiv = clone.querySelector("#qr_code");
        if (cloneQrDiv) {
            cloneQrDiv.innerHTML = "";
            new QRCode(cloneQrDiv, {
                text: qrText,
                width: 150,
                height: 100
            });
        }
        // Label for ORIGINAL
        const originalLabel = document.createElement("div");
        originalLabel.innerHTML = "<h4 style='text-align:center; margin-bottom: 5px;'></h4>";
        original.insertBefore(originalLabel, original.firstChild);

        // Label for COPY
        const copyLabel = document.createElement("div");
        copyLabel.innerHTML = "<h4 style='text-align:center; margin-bottom: 5px;'></h4>";
        clone.insertBefore(copyLabel, clone.firstChild);

        // Add space between original and copy
        clone.style.marginTop = "40px";
        clone.style.pageBreakBefore = "always";

        // Append copy
        parent.appendChild(clone);
    }

    // Delay print slightly for rendering
    setTimeout(() => {
        window.print();
    }, 500);
};
</script>


@endpush