document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.getElementById('signup-form');
    const signinForm = document.getElementById('signin-form');
    const infoContainer = document.getElementById('info-container'); // Div where data will be displayed

    // Handle sign-up form submission
    if (signupForm) {
        signupForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Collect form data
            const formData = new FormData(signupForm);

            const data = {
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                gender: formData.get('gender'),
                email: formData.get('signup-email'),
                password: formData.get('signup-password'),
            };

            // Validate data
            if (!data.first_name || !data.last_name || !data.email || !data.password) {
                alert('All fields are required!');
                return;
            }

            // Send data to backend
            fetch('http://localhost/DetyraProjektuse/backend/accinfo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        alert(result.message);
                        signupForm.reset();
                    } else {
                        alert(result.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    }

    if (infoContainer) {
        // Send GET request to displayinfo.php
        fetch('http://localhost/DetyraProjektuse/backend/displayinfo.php?fetch=user_info')
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to fetch user information');
                }
                return response.json(); // Parse the JSON response
            })
            .then((userInfo) => {
                // Check if the success flag is true and required fields exist
                if (userInfo.success) {
                    const userCard = `
                        <div class="card">
                            <div class="card-header">User Information</div>
                            <div class="card-content">
                                <p><strong>Name:</strong> ${userInfo.first_name}</p>
                                <p><strong>Last Name:</strong> ${userInfo.last_name}</p>
                                <p><strong>Gender:</strong> ${userInfo.gender}</p>
                                <p><strong>Email:</strong> ${userInfo.email}</p>
                            </div>
                        </div>
                    `;
                    infoContainer.innerHTML = userCard; // Populate the div with the card
                } else {
                    // Handle invalid data or backend errors
                    infoContainer.innerHTML = `<p>No user information available.</p>`;
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                infoContainer.innerHTML = '<p>An error occurred while fetching user information.</p>';
            });
    }


    if (signinForm) {
        signinForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Get email and password input values
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validate inputs
            if (!email || !password) {
                alert('Please enter both email and password.');
                return;
            }

            // Fetch users from the backend
            fetch('http://localhost/DetyraProjektuse/backend/signin.php')
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((result) => {
                    if (result.success && Array.isArray(result.users)) {
                        // Find the user with matching email and password
                        const matchedUser = result.users.find(
                            (user) => user.email === email && user.password === password
                        );

                        if (matchedUser) {
                            // Display user information
                            const userCard = `
                                <div class="card">
                                    <div class="card-header">User Information</div>
                                    <div class="card-content">
                                        <p><strong>Name:</strong> ${matchedUser.first_name} ${matchedUser.last_name}</p>
                                        <p><strong>Email:</strong> ${matchedUser.email}</p>
                                        <p><strong>Gender:</strong> ${matchedUser.gender}</p>
                                    </div>
                                </div>
                            `;
                            infoContainer.innerHTML = userCard;
                        } else {
                            // No match found
                            alert('Invalid email or password!');
                            infoContainer.innerHTML = ''; // Clear previous user info
                        }
                    } else {
                        alert('Failed to retrieve user data.');
                        console.error('Error:', result.message || 'Unknown error');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    }
});
