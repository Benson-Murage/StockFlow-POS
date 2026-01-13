# Advanced Input Field Debugging Guide

## **PERSISTENT TYPING ISSUES - Additional Investigation Needed**

Since basic fixes didn't resolve the issue, here are **deeper potential causes** to investigate:

### **1. CONTEXT VALUE OVERRIDE (Most Likely)**

**Problem**: Discount field uses `value={discount.toString()}` where `discount` comes from context
**Impact**: Context changes override local input, causing typing to be ignored

**Evidence in Code:**
```javascript
// Line 270 in CashCheckoutDialog.jsx
value={discount.toString()}

// Line 290 in MpesaCheckoutDialog.jsx  
value={discount.toString()}
```

**Root Cause**: When `setContextDiscount()` is called, it updates the context, which causes all components using that context to re-render with the context value, overwriting local input.

### **2. MATERIAL-UI VERSION COMPATIBILITY**

**Potential Issues:**
- `slotProps` prop may not work correctly in some MUI versions
- `InputProps` vs `slotProps.input` confusion
- TextField prop conflicts

**Check**: What Material-UI version is being used?
```bash
npm list @mui/material
```

### **3. REACT STRICT MODE INTERFERENCE**

**Problem**: Development mode may cause double renders, interfering with input focus
**Symptoms**: Cursor jumps, typing interrupted, events not firing

**Check**: Look for `<React.StrictMode>` wrapper in main app files

### **4. FORM PROPS CONFLICTS**

**Current Code:**
```javascript
<Dialog
    PaperProps={{
        component: 'form',
        onSubmit: handleSubmit,
    }}
>
```

**Problem**: Dialog acting as form may interfere with normal TextField behavior

### **5. BROWSER CONSOLE ERRORS**

**Check for:**
- JavaScript errors preventing event handlers
- CSS conflicts making fields uneditable
- z-index issues causing overlay problems
- Event propagation issues

## **IMMEDIATE DIAGNOSTIC STEPS:**

### **Step 1: Test with Simple HTML Input**

Replace Material-UI TextField with basic HTML input temporarily:

```javascript
// TEMPORARY TEST - Replace TextField with HTML input
<input
    type="text"
    value={amountReceived}
    onChange={(e) => setAmountReceived(e.target.value)}
    onFocus={(e) => e.target.select()}
    autoFocus={field.autoFocus}
    placeholder={field.placeholder}
    style={{
        width: '100%',
        padding: '16px',
        fontSize: '2rem',
        textAlign: 'center',
        border: '2px solid #ccc',
        borderRadius: '4px'
    }}
/>
```

### **Step 2: Check Browser Console**

1. Open browser DevTools (F12)
2. Go to Console tab
3. Try typing in each field
4. Look for:
   - Red error messages
   - Event handler not firing messages
   - React warnings about key props

### **Step 3: Test Input Event Propagation**

Add extensive logging to identify where events are failing:

```javascript
onChange={(event) => {
    console.log('=== INPUT EVENT DEBUG ===');
    console.log('Event type:', event.type);
    console.log('Target value:', event.target.value);
    console.log('Current state before:', amountReceived);
    console.log('Setting state to:', event.target.value);
    setAmountReceived(event.target.value);
    console.log('State setter called');
}}
```

### **Step 4: Isolate Context Issues**

Temporarily remove context dependency for testing:

```javascript
// Instead of:
value={discount.toString()}

// Use:
const [localDiscount, setLocalDiscount] = useState(discount.toString());
useEffect(() => {
    setLocalDiscount(discount.toString());
}, [discount]);
```

## **ADVANCED DEBUGGING TECHNIQUES:**

### **React DevTools Profiler**

1. Install React DevTools browser extension
2. Record typing session
3. Look for:
   - Unexpected re-renders
   - State update loops
   - Component unmount/remount cycles

### **Performance Monitor**

1. Open Chrome DevTools
2. Go to Performance tab
3. Record while typing
4. Look for:
   - Long tasks (>50ms) blocking input
   - Layout shifts causing cursor jumps
   - Memory leaks from event handlers

### **Event Breakpoints**

1. In DevTools Sources tab
2. Set breakpoint on:
   - `setState` calls
   - Input event handlers
   - Form submission handlers

## **QUICK FIXES TO TRY:**

### **Fix 1: Disable Context for Testing**
```javascript
// Replace context discount with local state
const [discountValue, setDiscountValue] = useState(discount.toString());

// In TextField:
value={discountValue}
onChange={(e) => setDiscountValue(e.target.value)}
```

### **Fix 2: Simplify Dialog Structure**
```javascript
// Remove form wrapper from Dialog
<Dialog open={open} onClose={handleClose}>
    <form onSubmit={handleSubmit}>
        {/* TextFields here */}
    </form>
</Dialog>
```

### **Fix 3: Use Native HTML Inputs**
```javascript
// Replace TextField with input for testing
<input
    name="amount_received"
    value={amountReceived}
    onChange={(e) => setAmountReceived(e.target.value)}
    type="text"
    autoFocus
    required
/>
```

## **WHAT TO REPORT BACK:**

1. **Console Errors**: Any red errors when typing
2. **Event Logs**: Do the console.log messages appear when typing?
3. **Simple HTML Test**: Does typing work with basic HTML input?
4. **Material-UI Version**: What MUI version is installed?
5. **React DevTools**: Any unusual re-render patterns?

This will help identify the exact root cause of the persistent typing issues.