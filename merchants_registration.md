
**Merchants registration instractions**


**General instractions**
-This is a new role for the project which is about allowing indiviual women merchants to register and sell their products and provide their services on the platform
-Create the required migrations - controllers - services - models - views - etc...
-Look at the vendor and provider registration flow and implemnt the same flow for the merchant registration
-Look at the vendor and provider registeration pages and implemnt the same ui and ux for the merchant 
-look at the migration files to see the database structure for vendor and provider and implemnt the same for merchant
-look at the controllers of vendors and providers to understant how the roles work in this project
-merchant should have the ability to upload his licence as pdf file
-merchant should have the ability to upload his uae id front and back as image file
-merchant should have the ability to add his small store location from google map
-merchant should have the ability to add his deliver to customer cabability and fee based on the emirate
-



**registeraition flow**
1-merchant info page and uae id page  -> ->2-merchant email verification page-> 3-merchant otp verification page -> 4-licence page

**merchant info**
-Email filed should be unique and not used before
-Phone filed should be unique and not used before
-Name filed should be unique and not used before
-Password filed should be confirmed
-logo
-small store location picked from google map - optional
-filed to upload or take a photo of the front side uae id
-filed to upload or take a photo of the back side uae id
-Deliver to customer cabability 
-Deliver to customer fee based on the emirate
**merchant otp authenticaiton page**
-otp filed should be confirmed
-otp filed should be sent to the phone
**merchant email authenticaiton page**
-please vefiry your email page


**Liecnce page**
registraition liecnce required as pdf file should be improted from  merchant and saved in his table
**Start date**
the start date of the licence should be the date of the registeration
**End date**
the end date of the licence should be the start date + the duration of the licence
**Duration**
the duration from start date to end date
**Status**
the status of the licence should be active
**Renewal date**
the renewal date of the licence should be the end date of the licence

**Instractions**

**Acitons**
-When the user click Don't have an account? **Create one now**
from this url **http://localhost:8000/login** show him page with option if he is a vendor , provider or merchant
-When the user click on merchant he will be redirected to the merchant registeration page
-When the user click on vendor he will be redirected to the vendor registeration page
-When the user click on provider he will be redirected to the provider registeration page


**OTP**

-Implement OTP as the otp implemented for vendor and provider registration


**UI**
run your bowser mcp to look for the best ui and ux for this registration flow and learn from them to implement it to our pages.

**Related database tables**
-No reltead database for this role please create it

