// File: js/handleGoogleLogin.js

// Main Google login handler function
function handleGoogleLogin(response) {
    console.log('Google login initiated');
    
    const id_token = response.credential;
    
    if (!id_token) {
        alert('No credential received from Google');
        return;
    }

    console.log('Processing Google login...');
    
    fetch('../classes/googleLogin.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ token: id_token })
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        
        return response.json();
    })

    .then(result => {
    console.log('Server response:', result);
        if (result.success === false) {
            alert('Login failed: ' + (result.message || 'Unknown error'));
            return;
        }

        if (result.needs_profile) {
            let role = result.role || 'student';
            if (role === 'student') {
                window.location.href = '/THESIS/pages/complete_profile_student.php';
            } else {
                window.location.href = '/THESIS/pages/complete_profile_teacher.php';
            }
        } else if (result.dashboard) {
            window.location.href = result.dashboard;
        } else {
            alert('Login successful but no redirect specified');
        }
    })

    .catch(err => {
        console.error("Google login error:", err);
        
        if (err.message.includes('Unexpected token')) {
            alert('Server error: The server returned HTML instead of JSON. Check server logs for PHP errors.');
        } else if (err.message.includes('HTTP error')) {
            alert('Server error: ' + err.message + '. Please check if the server file exists and is accessible.');
        } else {
            alert('Network error during login: ' + err.message);
        }
    });
}

//(for debugging)
function testConnection() {
    console.log('Testing connection...');
    
    fetch('../classes/test_debug.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ test: 'data' })
    })
    .then(response => {
        console.log('Test response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const json = JSON.parse(text);
            console.log('Parsed JSON:', json);
        } catch (e) {
            console.error('JSON parse error:', e);
        }
    })
    .catch(err => {
        console.error('Test connection error:', err);
    });
}

function initGoogleSignIn() {
    if (typeof google !== 'undefined' && google.accounts) {
        console.log('Google Sign-In library loaded');
    } else {
        console.log('Waiting for Google Sign-In library...');
        setTimeout(initGoogleSignIn, 500);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initGoogleSignIn();
});
document.addEventListener('DOMContentLoaded', function() {
    // Wait until Google client library is loaded
    function renderButton() {
        if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
            console.log('Google Sign-In library loaded');
            
            google.accounts.id.initialize({
                client_id: "1027047820121-8ttrsc7g4io22un3o971io4tnj961cbq.apps.googleusercontent.com",
                callback: handleGoogleLogin
            });

            google.accounts.id.renderButton(
                document.getElementById("googleSignInBtn"),
                { theme: "outline", size: "large", text: "signin_with" }
            );

            google.accounts.id.prompt();
        } else {
            console.log('Waiting for Google Sign-In library...');
            setTimeout(renderButton, 500);
        }
    }

    renderButton();
});

// You can also call testConnection() from browser console for debugging
// testConnection();