**General Instractions**
-Mark every step you complete in the step with a green checkmark.
-Ensure to complete all steps
-read these files before you start  'C:\Users\Admin\Desktop\TheProject\Production\theproject_backend\resources\js\components\merchant\ProductEditApp.vue'
'C:\Users\Admin\Desktop\TheProject\Production\theproject_backend\resources\js\components\merchant\SpecificationItem.vue'
'C:\Users\Admin\Desktop\TheProject\Production\theproject_backend\resources\js\components\merchant\SizeManagement.vue'

**Objective**
-Make the UX of the product edit of the vendor  using the 3 tabs same as the merchant.
http://localhost:8000/vendor/products/1/edit
-Basic info tap
-Images and colors tap
-Specifications tap

**Step 1 []**
-Make the UX and layout just like the merchant edit page using three taps 
-Maintain the existing UI pattern

**Step 2 []**
-Make sure the stock validation is working
-Make sure the stock validtion is using the correct order General > Color > Size

**Step 3 []**
-Make sure the color name is working and the color appears after selecting it
-Make sure the color code is working and it's color picker  appearing
-Make sure the image upload is working and the image appears after uploading it
-Make sure the remove image button is working and the image is removed after clicking it
-Make sure the color stock is working and it's not accepting values greater than the general stock
-Make sure the add color button is working and adding a new color form
-Make sure the remove color button is working and removing the color form
-Make sure the validation of required images is working 

**Step 4 []**
-Make sure the size management appearing after vendor enter the color name and stock value
-Make sure the size category is working and showing the three categories Clothes , shoes and hats
-Make sure the size name is working and it's showing the correct sizes for each category
-Make sure the size stock is working and it's not accepting values greater than the color stock
-Make sure the add size button is working and adding a new size form
-Make sure the remove size button is working and removing the size form 

**Step 5 []**
-Make sure the specifications is working and it's adding a new specification form
-Make sure the remove specification button is working and removing the specification form
-Make sure the validation of required specifications is working 
**Step 6 []**
-Make sure the update product button is working and saving the product after filling all the required fields
-Make sure the validation of required fields is working and it's not allowing to save the product without filling the required fields
-Make sure the validition showing the proper error
-Make sure the product update is saved to database correctlly
**UX Testing for Vendor Product Edit with 3 Tabs**
-Use playwright automation to test the product edition
-Use these credentials to login
email: gogoh3296@gmail.com
password: Fifa2021 
if you need to you
-Edit prodcut sizes , images , colors and specifications
-Ensure the product is updated correctly in the database
-Ensure the product is updated correctly in the UI and showing the new images 