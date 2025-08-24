const puppeteer = require('puppeteer');
const fs = require('fs');

async function testVendorRegistrationTranslations() {
    console.log('Starting vendor registration translation test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        defaultViewport: { width: 1280, height: 720 }
    });
    
    const page = await browser.newPage();
    
    try {
        // Test English version
        console.log('\n=== Testing English Version ===');
        await page.goto('http://localhost:8000/language/en');
        await page.waitForTimeout(1000);
        await page.goto('http://localhost:8000/register/vendor');
        await page.waitForTimeout(2000);
        
        // Take screenshot of English version
        await page.screenshot({ path: 'vendor_registration_english.png', fullPage: true });
        console.log('Screenshot saved: vendor_registration_english.png');
        
        // Check English elements
        const englishElements = await checkPageElements(page, 'English');
        
        // Test Arabic version
        console.log('\n=== Testing Arabic Version ===');
        await page.goto('http://localhost:8000/language/ar');
        await page.waitForTimeout(1000);
        await page.goto('http://localhost:8000/register/vendor');
        await page.waitForTimeout(2000);
        
        // Take screenshot of Arabic version
        await page.screenshot({ path: 'vendor_registration_arabic.png', fullPage: true });
        console.log('Screenshot saved: vendor_registration_arabic.png');
        
        // Check Arabic elements
        const arabicElements = await checkPageElements(page, 'Arabic');
        
        // Generate report
        generateReport(englishElements, arabicElements);
        
    } catch (error) {
        console.error('Test failed:', error);
    } finally {
        await browser.close();
    }
}

async function checkPageElements(page, language) {
    console.log(`\nChecking ${language} page elements...`);
    
    const results = {
        language: language,
        direction: null,
        title: null,
        formElements: {},
        stepLabels: [],
        buttons: [],
        hasArabicText: false
    };
    
    try {
        // Check page direction
        results.direction = await page.evaluate(() => {
            return document.documentElement.getAttribute('dir') || 'ltr';
        });
        console.log(`Direction: ${results.direction}`);
        
        // Check page title
        results.title = await page.title();
        console.log(`Title: ${results.title}`);
        
        // Check for Arabic text
        const pageText = await page.evaluate(() => document.body.textContent);
        results.hasArabicText = /[\u0600-\u06FF]/.test(pageText);
        console.log(`Has Arabic text: ${results.hasArabicText}`);
        
        // Check form elements
        const formElementIds = ['fullName', 'email', 'phone', 'password', 'confirmPassword'];
        
        for (const elementId of formElementIds) {
            try {
                const element = await page.$(`#${elementId}`);
                if (element) {
                    const placeholder = await page.evaluate(el => el.placeholder, element);
                    const label = await page.evaluate(el => {
                        const labelEl = document.querySelector(`label[for="${el.id}"]`);
                        return labelEl ? labelEl.textContent : null;
                    }, element);
                    
                    results.formElements[elementId] = {
                        found: true,
                        placeholder: placeholder,
                        label: label,
                        hasArabicPlaceholder: /[\u0600-\u06FF]/.test(placeholder || ''),
                        hasArabicLabel: /[\u0600-\u06FF]/.test(label || '')
                    };
                    
                    console.log(`${elementId}: placeholder="${placeholder}", label="${label}"`);
                } else {
                    results.formElements[elementId] = { found: false };
                    console.log(`${elementId}: NOT FOUND`);
                }
            } catch (error) {
                console.log(`Error checking ${elementId}:`, error.message);
                results.formElements[elementId] = { found: false, error: error.message };
            }
        }
        
        // Check step labels
        const stepElements = await page.$$('.step-label, .step-title, [class*="step"]');
        for (const stepEl of stepElements) {
            const text = await page.evaluate(el => el.textContent.trim(), stepEl);
            if (text) {
                results.stepLabels.push({
                    text: text,
                    hasArabicText: /[\u0600-\u06FF]/.test(text)
                });
            }
        }
        
        // Check buttons
        const buttonElements = await page.$$('button');
        for (const buttonEl of buttonElements) {
            const text = await page.evaluate(el => el.textContent.trim(), buttonEl);
            if (text) {
                results.buttons.push({
                    text: text,
                    hasArabicText: /[\u0600-\u06FF]/.test(text)
                });
            }
        }
        
    } catch (error) {
        console.error(`Error checking ${language} elements:`, error);
        results.error = error.message;
    }
    
    return results;
}

function generateReport(englishResults, arabicResults) {
    console.log('\n=== TRANSLATION TEST REPORT ===');
    
    const report = {
        timestamp: new Date().toISOString(),
        english: englishResults,
        arabic: arabicResults,
        issues: [],
        recommendations: []
    };
    
    // Check direction
    if (arabicResults.direction !== 'rtl') {
        report.issues.push('Arabic page should have dir="rtl" but has: ' + arabicResults.direction);
    }
    
    // Check Arabic text presence
    if (!arabicResults.hasArabicText) {
        report.issues.push('Arabic page does not contain Arabic text');
    }
    
    // Check form elements
    Object.keys(arabicResults.formElements).forEach(elementId => {
        const arabicEl = arabicResults.formElements[elementId];
        const englishEl = englishResults.formElements[elementId];
        
        if (arabicEl.found && englishEl.found) {
            if (!arabicEl.hasArabicPlaceholder && arabicEl.placeholder) {
                report.issues.push(`${elementId} placeholder not translated to Arabic: "${arabicEl.placeholder}"`);
            }
            if (!arabicEl.hasArabicLabel && arabicEl.label) {
                report.issues.push(`${elementId} label not translated to Arabic: "${arabicEl.label}"`);
            }
        }
    });
    
    // Check buttons
    arabicResults.buttons.forEach((button, index) => {
        if (!button.hasArabicText && button.text.length > 0) {
            report.issues.push(`Button not translated to Arabic: "${button.text}"`);
        }
    });
    
    // Generate recommendations
    if (report.issues.length === 0) {
        report.recommendations.push('All translations appear to be working correctly!');
    } else {
        report.recommendations.push('Update translation files in resources/lang/ar/messages.php');
        report.recommendations.push('Ensure all UI elements use the $t() translation function');
        report.recommendations.push('Clear Laravel cache after updating translations');
    }
    
    // Print summary
    console.log(`\nIssues found: ${report.issues.length}`);
    report.issues.forEach((issue, index) => {
        console.log(`${index + 1}. ${issue}`);
    });
    
    console.log('\nRecommendations:');
    report.recommendations.forEach((rec, index) => {
        console.log(`${index + 1}. ${rec}`);
    });
    
    // Save report to file
    fs.writeFileSync('translation_test_report.json', JSON.stringify(report, null, 2));
    console.log('\nDetailed report saved to: translation_test_report.json');
}

// Run the test
if (require.main === module) {
    testVendorRegistrationTranslations().catch(console.error);
}

module.exports = { testVendorRegistrationTranslations };