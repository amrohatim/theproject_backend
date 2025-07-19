# Hierarchical Stock Validation Test Plan
## Vue.js Tabbed Product Creation Interface

### Overview
This document provides a comprehensive test plan for verifying the hierarchical stock validation functionality in the new Vue.js tabbed product creation interface for merchants.

### Test Environment
- **URL**: `http://localhost:8000/merchant/products/create`
- **Interface**: Vue.js tabbed interface (ProductCreateApp.vue)
- **Authentication**: Merchant account required

---

## Test Scenarios

### 1. General Stock (Total Product Stock) Validation

#### Test 1.1: Valid Stock Input
**Objective**: Verify that the total stock field accepts valid numeric values

**Steps**:
1. Navigate to merchant product creation page
2. Wait for Vue.js app to load (loading spinner disappears)
3. Locate the "Total Stock" field in the Basic Info tab
4. Enter a valid positive number (e.g., 100)
5. Verify the value is accepted and displayed

**Expected Result**: 
- ✅ Field accepts the value
- ✅ No validation errors shown
- ✅ Value serves as master stock limit

#### Test 1.2: Negative Stock Prevention
**Objective**: Verify that negative stock values are prevented

**Steps**:
1. Enter a negative value (e.g., -10) in the Total Stock field
2. Tab out or trigger validation

**Expected Result**:
- ✅ Value is either rejected or auto-corrected to 0
- ✅ Appropriate error message or feedback shown

#### Test 1.3: Zero Stock Handling
**Objective**: Verify zero stock is handled correctly

**Steps**:
1. Enter 0 in the Total Stock field
2. Observe behavior

**Expected Result**:
- ✅ Zero value is accepted
- ✅ Color stock allocation should be limited accordingly

---

### 2. Color Stock Allocation Validation

#### Test 2.1: Add First Color
**Objective**: Verify color addition and stock allocation

**Steps**:
1. Set Total Stock to 100
2. Click "Add First Color" or navigate to Colors & Images tab
3. Add a color variant
4. Set color name (e.g., "Red")
5. Set color stock to 60

**Expected Result**:
- ✅ Color is added successfully
- ✅ Stock allocation is within total stock limit
- ✅ Stock summary appears showing allocation

#### Test 2.2: Stock Allocation Summary Display
**Objective**: Verify stock allocation summary shows correct information

**Steps**:
1. After adding a color with stock, locate the "Stock Allocation Summary" section
2. Verify the following elements are present and accurate:
   - Total Stock: 100
   - Allocated Stock: 60
   - Remaining Stock: 40
   - Progress bar showing ~60% allocation

**Expected Result**:
- ✅ All summary elements are visible
- ✅ Calculations are correct
- ✅ Progress bar reflects allocation percentage
- ✅ Progress bar color is blue (normal allocation)

#### Test 2.3: Over-Allocation Prevention
**Objective**: Verify that color stock cannot exceed total stock

**Steps**:
1. With Total Stock = 100 and first color = 60
2. Add a second color
3. Try to set second color stock to 50 (total would be 110)

**Expected Result**:
- ✅ Value is auto-corrected to maximum allowed (40)
- ✅ Warning message appears
- ✅ Stock summary shows over-allocation warning
- ✅ Progress bar turns red when over-allocated

#### Test 2.4: Multiple Color Allocation
**Objective**: Test allocation across multiple colors

**Steps**:
1. Set Total Stock to 200
2. Add 3 colors with stocks: 80, 70, 50 (total = 200)
3. Verify all allocations are accepted
4. Try to increase any color stock beyond available

**Expected Result**:
- ✅ Valid allocations are accepted
- ✅ Over-allocation is prevented with auto-correction
- ✅ Stock summary updates in real-time

---

### 3. Size Stock Distribution (Within Colors)

#### Test 3.1: Size Management Visibility
**Objective**: Verify size management appears when color has stock

**Steps**:
1. Add a color with name and stock > 0
2. Look for size management section within the color card

**Expected Result**:
- ✅ Size management section becomes visible
- ✅ "Add Size" button is available
- ✅ Message shows when color needs name and stock

#### Test 3.2: Size Stock Allocation
**Objective**: Test size stock allocation within color limits

**Steps**:
1. Set color stock to 50
2. Add sizes (S, M, L) with stocks: 15, 20, 15 (total = 50)
3. Verify allocations are accepted

**Expected Result**:
- ✅ Size stocks are accepted when within color limit
- ✅ Size stock total cannot exceed color stock
- ✅ Real-time validation feedback

#### Test 3.3: Size Over-Allocation Prevention
**Objective**: Verify size stock cannot exceed color stock

**Steps**:
1. With color stock = 50 and existing size allocations
2. Try to set a size stock that would exceed color limit

**Expected Result**:
- ✅ Over-allocation is prevented
- ✅ Auto-correction occurs
- ✅ Appropriate feedback message shown

---

### 4. Validation Rules and Error Handling

#### Test 4.1: Form Submission Validation
**Objective**: Verify form cannot be submitted with invalid stock allocation

**Steps**:
1. Set up an over-allocation scenario
2. Fill required fields (name, category, price)
3. Attempt to submit the form

**Expected Result**:
- ✅ Form submission is prevented
- ✅ Validation errors are shown
- ✅ User is guided to correct tab
- ✅ Clear error messages displayed

#### Test 4.2: Required Color Validation
**Objective**: Verify at least one color with stock is required

**Steps**:
1. Fill basic product info but don't add any colors
2. Attempt to submit

**Expected Result**:
- ✅ Validation error about missing colors
- ✅ User directed to Colors & Images tab

#### Test 4.3: Tab Navigation with Errors
**Objective**: Verify error handling guides user to correct tab

**Steps**:
1. Create validation errors in different tabs
2. Attempt form submission
3. Observe tab switching behavior

**Expected Result**:
- ✅ User is automatically switched to tab with errors
- ✅ Error messages are clearly visible
- ✅ Tab indicators show error state

---

### 5. Real-Time Updates and Feedback

#### Test 5.1: Progress Bar Updates
**Objective**: Verify progress bar updates in real-time

**Steps**:
1. Start with Total Stock = 100
2. Gradually add color stock allocations
3. Observe progress bar changes

**Expected Result**:
- ✅ Progress bar updates immediately
- ✅ Color changes from blue to red when over-allocated
- ✅ Percentage calculation is accurate

#### Test 5.2: Stock Correction Feedback
**Objective**: Verify visual feedback for auto-corrections

**Steps**:
1. Trigger an auto-correction scenario
2. Observe visual feedback

**Expected Result**:
- ✅ Input field shows visual feedback (border color change)
- ✅ Correction message appears
- ✅ Message auto-dismisses after timeout

---

### 6. Edge Cases and Error Scenarios

#### Test 6.1: Rapid Input Changes
**Objective**: Test system stability with rapid input changes

**Steps**:
1. Rapidly change stock values in multiple fields
2. Observe system behavior

**Expected Result**:
- ✅ System remains stable
- ✅ Validations work correctly
- ✅ No race conditions or errors

#### Test 6.2: Browser Refresh Handling
**Objective**: Verify data persistence behavior

**Steps**:
1. Fill form partially
2. Refresh browser
3. Observe data state

**Expected Result**:
- ✅ Form resets to initial state (expected behavior)
- ✅ No JavaScript errors
- ✅ Vue.js app reinitializes correctly

---

## Manual Testing Checklist

### Pre-Test Setup
- [ ] Merchant account is logged in
- [ ] Navigate to `/merchant/products/create`
- [ ] Verify Vue.js app loads (no loading spinner)
- [ ] Check browser console for errors

### Basic Info Tab Tests
- [ ] Total Stock accepts valid positive numbers
- [ ] Negative values are prevented/corrected
- [ ] Zero values are handled correctly
- [ ] Field validation works on blur/change

### Colors & Images Tab Tests
- [ ] Add color functionality works
- [ ] Color stock allocation respects total stock
- [ ] Stock summary displays correctly
- [ ] Progress bar updates in real-time
- [ ] Over-allocation prevention works
- [ ] Auto-correction provides feedback

### Size Management Tests
- [ ] Size section appears when color has stock
- [ ] Size stock allocation works within color limits
- [ ] Size over-allocation is prevented
- [ ] Size stock validation provides feedback

### Form Validation Tests
- [ ] Invalid stock configurations prevent submission
- [ ] Error messages are clear and helpful
- [ ] Tab navigation works with errors
- [ ] Required field validation works

### Integration Tests
- [ ] All validation layers work together
- [ ] Real-time updates are smooth
- [ ] No JavaScript console errors
- [ ] Mobile responsiveness (if applicable)

---

## Success Criteria

The hierarchical stock validation system is considered working correctly if:

1. **Data Integrity**: Stock allocations never exceed limits at any level
2. **User Experience**: Clear feedback and guidance for corrections
3. **Real-Time Updates**: Immediate validation and visual feedback
4. **Error Prevention**: Invalid forms cannot be submitted
5. **Auto-Correction**: Over-allocations are automatically adjusted
6. **Visual Indicators**: Progress bars and status indicators work correctly

---

## Known Issues to Watch For

- Vue.js reactivity delays
- Race conditions with rapid input
- Browser compatibility issues
- Mobile touch interaction problems
- Network timeout handling
- Form state management edge cases

---

## Reporting Results

For each test scenario, document:
- ✅ **PASS**: Feature works as expected
- ❌ **FAIL**: Feature doesn't work, include error details
- ⚠️ **PARTIAL**: Feature works but has minor issues
- 🔄 **NEEDS RETRY**: Test inconclusive, needs re-testing

Include screenshots for any failures or unexpected behavior.
