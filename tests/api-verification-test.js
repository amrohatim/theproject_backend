const https = require('https');

// Test data
const testData = {
  name: 'Test Merchant API',
  email: `test.api.${Date.now()}@example.com`,
  phone: `+971501234${Date.now().toString().slice(-3)}`,
  password: 'TestPassword123!',
  password_confirmation: 'TestPassword123!',
  business_name: 'Test Business API',
  business_license: `BL${Date.now()}`,
  business_description: 'Test business for API testing',
  address: 'Test Address 123',
  city: 'Dubai',
  state: 'Dubai',
  postal_code: '12345',
  country: 'AE'
};

function makeRequest(path, method, data) {
  return new Promise((resolve, reject) => {
    const postData = JSON.stringify(data);
    
    const options = {
      hostname: 'dala3chic.com',
      port: 443,
      path: path,
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'User-Agent': 'API-Test/1.0'
      }
    };

    const req = https.request(options, (res) => {
      let responseData = '';
      
      res.on('data', (chunk) => {
        responseData += chunk;
      });
      
      res.on('end', () => {
        try {
          const jsonData = JSON.parse(responseData);
          resolve({
            status: res.statusCode,
            headers: res.headers,
            data: jsonData
          });
        } catch (e) {
          resolve({
            status: res.statusCode,
            headers: res.headers,
            data: responseData,
            parseError: e.message
          });
        }
      });
    });

    req.on('error', (e) => {
      reject(e);
    });

    if (data) {
      req.write(postData);
    }
    
    req.end();
  });
}

async function testEmailVerificationAPI() {
  console.log('🚀 Starting Email Verification API Test...');
  console.log(`📧 Test email: ${testData.email}`);
  
  try {
    // Step 1: Test health check
    console.log('📍 Step 1: Testing API health check...');
    const healthResponse = await makeRequest('/api/health-check', 'GET');
    console.log('✅ Health check response:', healthResponse.status, healthResponse.data);
    
    if (healthResponse.status !== 200) {
      throw new Error('API health check failed');
    }
    
    // Step 2: Submit merchant registration info
    console.log('📍 Step 2: Submitting merchant registration info...');
    const registrationResponse = await makeRequest('/api/merchant-registration/info', 'POST', testData);
    console.log('📝 Registration response:', registrationResponse.status, JSON.stringify(registrationResponse.data, null, 2));
    
    if (registrationResponse.status !== 200 && registrationResponse.status !== 201) {
      console.error('❌ Registration failed:', registrationResponse);
      return;
    }
    
    const registrationToken = registrationResponse.data.registration_token;
    console.log('🔑 Registration token:', registrationToken);
    
    if (!registrationToken) {
      console.error('❌ No registration token received');
      return;
    }
    
    // Step 3: Wait a moment for email to be sent
    console.log('📍 Step 3: Waiting for email to be sent...');
    await new Promise(resolve => setTimeout(resolve, 3000));
    
    // Step 4: Test verification with invalid code
    console.log('📍 Step 4: Testing with invalid verification code...');
    const invalidVerificationResponse = await makeRequest('/api/merchant-registration/verify-email', 'POST', {
      registration_token: registrationToken,
      verification_code: '123456'
    });
    console.log('📝 Invalid verification response:', invalidVerificationResponse.status, JSON.stringify(invalidVerificationResponse.data, null, 2));
    
    // Step 5: Test verification with empty code
    console.log('📍 Step 5: Testing with empty verification code...');
    const emptyVerificationResponse = await makeRequest('/api/merchant-registration/verify-email', 'POST', {
      registration_token: registrationToken,
      verification_code: ''
    });
    console.log('📝 Empty verification response:', emptyVerificationResponse.status, JSON.stringify(emptyVerificationResponse.data, null, 2));
    
    // Step 6: Test verification with wrong token
    console.log('📍 Step 6: Testing with wrong registration token...');
    const wrongTokenResponse = await makeRequest('/api/merchant-registration/verify-email', 'POST', {
      registration_token: 'wrong-token',
      verification_code: '123456'
    });
    console.log('📝 Wrong token response:', wrongTokenResponse.status, JSON.stringify(wrongTokenResponse.data, null, 2));
    
    console.log('✅ API verification test completed!');
    console.log('💡 To find the actual verification code, check Laravel logs:');
    console.log('   tail -f storage/logs/laravel.log | grep "verification code"');
    
  } catch (error) {
    console.error('❌ Test failed:', error);
  }
}

// Test resend functionality
async function testResendVerification() {
  console.log('🔄 Testing resend verification...');
  
  try {
    // First create a registration
    const registrationResponse = await makeRequest('/api/merchant-registration/info', 'POST', {
      ...testData,
      email: `resend.test.${Date.now()}@example.com`
    });
    
    if (registrationResponse.status === 200 || registrationResponse.status === 201) {
      const registrationToken = registrationResponse.data.registration_token;
      
      // Test resend
      const resendResponse = await makeRequest('/api/merchant-registration/resend-email-verification', 'POST', {
        registration_token: registrationToken
      });
      
      console.log('📝 Resend response:', resendResponse.status, JSON.stringify(resendResponse.data, null, 2));
    }
  } catch (error) {
    console.error('❌ Resend test failed:', error);
  }
}

// Run tests
async function runAllTests() {
  await testEmailVerificationAPI();
  console.log('\n' + '='.repeat(50) + '\n');
  await testResendVerification();
}

if (require.main === module) {
  runAllTests()
    .then(() => {
      console.log('🎉 All tests completed');
      process.exit(0);
    })
    .catch((error) => {
      console.error('💥 Tests failed:', error);
      process.exit(1);
    });
}

module.exports = { testEmailVerificationAPI, testResendVerification };
