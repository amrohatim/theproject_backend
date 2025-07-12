**First step validation rules for provider**
-IF the email is verified add a value to the 'email_verified' column in the users table.
-IF the business name is found in the database throw a validation error "Business name is already taken".
-Check IF the email is found in the database and this email has registration step with value 'verified' throw a validation error "You have a registered company with this email you cannot create two accounts with the same email".
-Check IF the email is found in the database and this email has registration step with value 'license_completed' throw a validation error "You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.".
-Check IF the phone is found in the database and this phone has registration step with value 'verified' throw a validation error "You have a registered company with this phone you cannot create two accounts with the same phone".
-IF the email exist and phone has not been verified yet skip the email verification and move the user to the phone verification step.
-IF the phone exist and it has been verified and the email exist and has been verified but the registration step dont have value 'verified' or 'license_completed' skip the email verification and the phone verification steps and move the user to the license upload step.
