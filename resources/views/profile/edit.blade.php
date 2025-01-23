@extends('layouts.dashboardlayout')

@section('content')
<div class="card profile-card">
    <div class="card-body">
        <h5 class="card-title">User Profile</h5>
        <hr>
        <p class="card-text mb-1">Name: {{ Auth::user()->name }}</p>
        <p class="card-text mb-1">Email: {{ Auth::user()->email }}</p>
        <p class="card-text mb-1">Referral Code: <span id="refCode" onclick="copyToClipboard('refCode')" style="text-decoration: underline; cursor:pointer;" title="Click to Copy">{{ Auth::user()->refercode }}</span></p>
        <p class="card-text mb-1">Referral Link: <a href="#" id="refLink" onclick="copyToClipboard('refLink')" style="text-decoration: underline; cursor:pointer;" title="Click to Copy">{{ url('register?code=' .Auth::user()->refercode) }}</a></p>
    </div>
</div>

<div class="card mt-3">
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
</div>
@endsection

@push('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateReferralForm = document.getElementById('generateReferralForm');

        generateReferralForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(generateReferralForm);
            const name = formData.get('name');
            const email = formData.get('email');

            // Example of how to generate a referral code (replace with your logic)
            const referralCode = generateReferralCode(name, email);

            // Hash the referral code
            const hashedReferralCode = hashCode(referralCode);

            // Update referral code display
            document.getElementById('generatedRefCode').innerText = hashedReferralCode;

            // Generate referral link
            const referralLink = '{{ url('register?code=') }}' + hashedReferralCode;
            const refLinkElement = document.getElementById('generatedRefLink');
            refLinkElement.href = referralLink;
            refLinkElement.innerText = referralLink;

            // Optional: Display the generated link or perform further actions
            console.log('Generated Referral Code:', referralCode);
            console.log('Generated Hashed Referral Code:', hashedReferralCode);
            console.log('Generated Referral Link:', referralLink);

            // Save the generated referral code/link to the database
            saveReferralCode(hashedReferralCode, referralLink);
        });
    });

    function generateReferralCode(name, email) {
        // Example of a simple logic to generate a referral code
       
        return name.slice(0, 3).toUpperCase() + email.slice(0, 3).toUpperCase() + Math.floor(Math.random() * 1000);
    }

    function hashCode(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            var char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(16); // Convert to hexadecimal
    }

    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.innerText || element.textContent;

        // Create a temporary textarea to copy the text
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);

        // Select the text and copy it to the clipboard
        textarea.select();
        document.execCommand('copy');

        // Remove the temporary textarea
        document.body.removeChild(textarea);
    }

    function saveReferralCode(code, link) {
        // Make an AJAX request to save the referral code/link to the database
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
</script>
@endpush
