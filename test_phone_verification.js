const { chromium } = require('playwright');

async function testPhoneVerification() {
    console.log('🚀 Starting Phone Verification Flow Test...\n');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 1000 // Slow down for better visibility
    });
    
    const context = await browser.newContext({
        viewport: { width: 1280, height: 720 }
    });
    
    const page = await context.newPage();
    
    // Listen for console logs
    page.on('console', msg => {
        console.log(`🖥️  Browser Console [${msg.type()}]:`, msg.text());
    });
    
    // Listen for network requests
    page.on('request', request => {
        if (request.url().includes('send-phone-otp') || request.url().includes('verify-phone-otp')) {
            console.log(`📡 Network Request: ${request.method()} ${request.url()}`);
        }
    });
    
    // Listen for network responses
    page.on('response', response => {
        if (response.url().includes('send-phone-otp') || response.url().includes('verify-phone-otp')) {
            console.log(`📨 Network Response: ${response.status()} ${response.url()}`);
        }
    });
    
    try {
        // Navigate to phone verification page
        console.log('📱 Navigating to phone verification page...');
        await page.goto('https://dala3chic.com/register/provider/phone-verification?token=9bc930182ff71e08b293d009c291dffb97002e46401b5e9cbba19069644af63f');
        
        // Wait for page to load
        await page.waitForLoadState('networkidle');
        
        // Take screenshot of initial state
        await page.screenshot({ path: 'phone_verification_initial.png' });
        console.log('📸 Screenshot saved: phone_verification_initial.png');
        
        // Check if send button is visible
        const sendButton = await page.locator('#send-otp-btn');
        const isVisible = await sendButton.isVisible();
        console.log(`✅ Send OTP button visible: ${isVisible}`);
        
        // Check if OTP form is initially hidden
        const otpForm = await page.locator('#otp-form-section');
        const isOtpFormHidden = await otpForm.isHidden();
        console.log(`✅ OTP form initially hidden: ${isOtpFormHidden}`);
        
        // Click send verification code button
        console.log('🔄 Clicking "Send Verification Code" button...');
        await sendButton.click();
        
        // Wait for API response and UI update
        await page.waitForTimeout(3000);
        
        // Check if OTP form is now visible
        const isOtpFormVisible = await otpForm.isVisible();
        console.log(`✅ OTP form now visible: ${isOtpFormVisible}`);
        
        // Check if send button is now hidden
        const sendSection = await page.locator('#send-otp-section');
        const isSendSectionHidden = await sendSection.isHidden();
        console.log(`✅ Send section now hidden: ${isSendSectionHidden}`);
        
        // Take screenshot after sending OTP
        await page.screenshot({ path: 'phone_verification_otp_form.png' });
        console.log('📸 Screenshot saved: phone_verification_otp_form.png');
        
        if (isOtpFormVisible) {
            // Enter the hardcoded OTP
            console.log('🔢 Entering hardcoded OTP: 666666...');
            const otpInput = await page.locator('#otp_code');
            await otpInput.fill('666666');
            
            // Click verify button
            console.log('✅ Clicking "Verify Phone Number" button...');
            const verifyButton = await page.locator('#verify-otp-btn');
            await verifyButton.click();
            
            // Wait for verification response
            await page.waitForTimeout(3000);
            
            // Take screenshot after verification
            await page.screenshot({ path: 'phone_verification_after_verify.png' });
            console.log('📸 Screenshot saved: phone_verification_after_verify.png');
            
            // Check for success message or redirect
            const currentUrl = page.url();
            console.log(`🌐 Current URL after verification: ${currentUrl}`);
            
            // Check for any alert messages
            const alertContainer = await page.locator('#alert-container');
            const alertVisible = await alertContainer.isVisible();
            if (alertVisible) {
                const alertText = await alertContainer.textContent();
                console.log(`🚨 Alert message: ${alertText}`);
            }
        }
        
        console.log('\n✅ Phone verification flow test completed successfully!');
        
    } catch (error) {
        console.error('❌ Test failed:', error);
        await page.screenshot({ path: 'phone_verification_error.png' });
        console.log('📸 Error screenshot saved: phone_verification_error.png');
    } finally {
        await browser.close();
    }
}

// Run the test
testPhoneVerification().catch(console.error);
