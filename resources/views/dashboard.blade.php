@extends('layouts.dashboardlayout')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Dashboard</h4>
            <h4 class="card-title">Points: {{ ($network * 100) + (count($mynetworks) * 50) }}</h4>
        </div>
    </div>
</div>

<!-- <div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Generate Referral Link</h5>
        <hr>
        <form id="generateReferralForm">
            <div class="form-group">
                <label for="inputName">Your Name</label>
                <input type="text" class="form-control" id="inputName" name="name" required>
            </div>
            <div class="form-group">
                <label for="inputEmail">Your Email</label>
                <input type="email" class="form-control" id="inputEmail" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate Link</button>
        </form>
        <p class="card-text mt-3">Generated Referral Code: <span id="generatedRefCode"></span></p>
        <p class="card-text">Generated Referral Link: <a href="#" id="generatedRefLink" style="text-decoration: underline; cursor:pointer;" title="Click to Copy"></a></p>
    </div>
</div> -->

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Refer Details</h5>
        <hr>
        <canvas id="myChart"></canvas>
    </div>
</div>
@endsection

@push('custom_js')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const generateReferralForm = document.getElementById('generateReferralForm');

        generateReferralForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(generateReferralForm);
            const name = formData.get('name');
            const email = formData.get('email');

            const referralCode = generateReferralCode(name, email);
            const hashedReferralCode = hashCode(referralCode);

            document.getElementById('generatedRefCode').innerText = hashedReferralCode;

            const referralLink = '{{ url('register?code=') }}' + hashedReferralCode;
            const refLinkElement = document.getElementById('generatedRefLink');
            refLinkElement.href = referralLink;
            refLinkElement.innerText = referralLink;

            saveReferralCode(hashedReferralCode, referralLink);
        });
    });

    function generateReferralCode(name, email) {
        return name.slice(0, 3).toUpperCase() + email.slice(0, 3).toUpperCase() + Math.floor(Math.random() * 1000);
    }

    function hashCode(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            var char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash.toString(16);
    }

    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.innerText || element.textContent;

        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);

        textarea.select();
        document.execCommand('copy');

        document.body.removeChild(textarea);
    }

    function saveReferralCode(code, link) {
        fetch('{{ route('referrals.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code, link: link })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Referral code/link saved successfully.');
            } else {
                console.log('Failed to save referral code/link.');
            }
        })
        .catch(error => {
            console.error('Error saving referral code/link:', error);
        });
    }

    var canvas = document.getElementById('myChart');
    var datalabels = JSON.parse(@json($datelabel));
    var datedata = JSON.parse(@json($datedata));
    var data = {
        labels: datalabels,
        datasets: [
            {
                label: "Referral User",
                fill: true,
                lineTension: 0.1,
                backgroundColor: "rgba(75,192,192,0.4)",
                borderColor: "rgba(75,192,192,1)",
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 5,
                pointHitRadius: 10,
                data: datedata,
            }
        ]
    };
    
    var option = {
      showLines: true
    };
    var myLineChart = Chart.Line(canvas,{
      data:data,
      options:option
    });
</script>
@endpush
