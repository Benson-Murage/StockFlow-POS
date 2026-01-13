import React from "react";
import Button from "@mui/material/Button";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogTitle from "@mui/material/DialogTitle";
import { Box, Grid, IconButton, TextField, FormControlLabel, Checkbox } from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";
import PrintReceiptModal from "@/Components/PrintReceiptModal";
import PercentIcon from '@mui/icons-material/Percent';
import SmartphoneIcon from '@mui/icons-material/Smartphone';
import InputAdornment from '@mui/material/InputAdornment';
import { router } from '@inertiajs/react';
import axios from "axios";
import Swal from "sweetalert2";
import { usePage } from "@inertiajs/react";
import { useState, useEffect, useContext } from 'react';

import { useSales as useCart } from '@/Context/SalesContext';
import { SharedContext } from "@/Context/SharedContext";
import { useCurrencyFormatter, toNumeric } from "@/lib/currencyFormatter";
import { useCurrencyStore } from "@/stores/currencyStore";

console.log('MpesaCheckoutDialog component loaded');
export default function MpesaCheckoutDialog({ disabled }) {
    const formatCurrency = useCurrencyFormatter();
    const currencySymbol = useCurrencyStore((state) => state.settings.currency_symbol);
    const return_sale = usePage().props.return_sale;
    const return_sale_id = usePage().props.sale_id;
    const edit_sale = usePage().props.edit_sale;
    const edit_sale_id = usePage().props.sale_id;

    const { cartState, cartTotal, totalProfit, emptyCart, charges, totalChargeAmount, finalTotal, discount, setDiscount: setContextDiscount, calculateChargesWithDiscount } = useCart();
    const { selectedCustomer, saleDate, saleTime } = useContext(SharedContext);
    const [loading, setLoading] = useState(false);

    const [showPrintModal, setShowPrintModal] = useState(false);
    const [receiptData, setReceiptData] = useState(null);
    const autoOpenPrintSetting = usePage().props.settings?.auto_open_print_dialog ?? '1';
    const [openPrintDialog, setOpenPrintDialog] = useState(autoOpenPrintSetting === '1');

    const [amountReceived, setAmountReceived] = useState('0');
    const [recalculatedCharges, setRecalculatedCharges] = useState(totalChargeAmount);
    const [mpesaPhone, setMpesaPhone] = useState('');
    const [note, setNote] = useState('');
    const [discountValue, setDiscountValue] = useState(discount.toString());
    const isMobile = window.innerWidth < 768;

    // Calculate reactive final total with discount
    const reactiveFinalTotal = (cartTotal - discount) + recalculatedCharges;

    // Initialize recalculated charges with optimized dependencies to prevent unnecessary re-renders
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            setRecalculatedCharges(calculateChargesWithDiscount(discount));
        }, 100);
        return () => clearTimeout(timeoutId);
    }, [charges, discount]); // Removed cartTotal to prevent frequent re-renders

    // Sync local discount with context only when dialog is closed
    useEffect(() => {
        if (!open) {
            setDiscountValue(discount.toString());
        }
    }, [discount, open]);

    const handleDiscountChange = (event) => {
        const inputDiscount = event.target.value;
        console.log('Mpesa discount changed:', inputDiscount);
        setDiscountValue(inputDiscount);
        
        const newDiscount = inputDiscount !== "" ? parseFloat(inputDiscount) : 0;
        setContextDiscount(newDiscount);

        const recalculatedChargeAmount = calculateChargesWithDiscount(newDiscount);
        setRecalculatedCharges(recalculatedChargeAmount);
    };

    // Check if M-Pesa is properly configured
    const mpesaEnabled = usePage().props.mpesa_enabled ?? false;
    const mpesaEnvConfigured = usePage().props.mpesa_env_configured ?? false;
    const isMpesaAvailable = mpesaEnabled && mpesaEnvConfigured;

    const [open, setOpen] = React.useState(false);

    const handleClickOpen = () => {
        if (!isMpesaAvailable) {
            Swal.fire({
                title: "M-Pesa Not Configured",
                text: "Please configure M-Pesa in Settings → Misc Settings and add credentials to .env file.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }
        console.log('Mpesa dialog opened, current amountReceived:', amountReceived);
        console.log('Current mpesaPhone:', mpesaPhone);
        console.log('Current note:', note);
        console.log('Current discountValue:', discountValue);
        // Don't reset discount - let user keep their input
        // Only set amount if it's currently empty or zero - preserve user's typing
        if (!amountReceived || amountReceived === '0') {
            const calculatedAmount = ((cartTotal - discount) + recalculatedCharges).toString();
            setAmountReceived(calculatedAmount);
            console.log('Mpesa amount set to:', calculatedAmount);
        } else {
            console.log('Preserving existing amount:', amountReceived);
        }
        // Don't reset mpesaPhone or note - let user keep their input
        setOpen(true);
    };

    const handleClose = () => {
        // Don't reset note, discount, or mpesaPhone - preserve user's input when closing
        setAmountReceived('0')
        setOpen(false);
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        if (loading) return;
        setLoading(true);

        const formData = new FormData(event.currentTarget);
        const formJson = Object.fromEntries(formData.entries());

        // Sanitize numeric fields
        formJson.amount_received = toNumeric(formJson.amount_received);
        formJson.discount = toNumeric(formJson.discount);
        formJson.net_total = toNumeric((cartTotal - discount) + recalculatedCharges);
        formJson.mpesa_phone = mpesaPhone;

        formJson.cartItems = cartState.map(item => ({
            ...item,
            price: toNumeric(item.price),
            quantity: toNumeric(item.quantity),
            discount: toNumeric(item.discount || 0)
        }));
        formJson.charges = charges.map(c => ({
            ...c,
            rate_value: toNumeric(c.rate_value)
        }));
        formJson.profit_amount = totalProfit - discount;
        formJson.sale_date = saleDate;
        formJson.sale_time = saleTime;
        formJson.payments = [{
            payment_method: 'MPesa',
            amount: parseFloat(formJson.amount_received),
            phone: mpesaPhone
        }];
        formJson.contact_id = selectedCustomer.id
        formJson.return_sale = return_sale;
        formJson.return_sale_id = return_sale_id;
        formJson.edit_sale_id = edit_sale_id;
        formJson.edit_sale = edit_sale;

        axios.post('/pos/checkout', formJson)
            .then((resp) => {
                Swal.fire({
                    title: "Success!",
                    text: resp.data.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 300,
                    timerProgressBar: true,
                });
                emptyCart() //Clear the cart from the Context API
                setAmountReceived('0')
                // Don't reset context discount, mpesaPhone, or note - let them persist for next transaction
                if (!resp.data?.mpesa_pending && openPrintDialog && resp.data.receipt) {
                    setReceiptData(resp.data.receipt);
                    setShowPrintModal(true);
                } else {
                    router.visit('/receipt/' + resp.data.sale_id)
                }
                axios.get('/sale-notification/' + resp.data.sale_id)
                    .then((resp) => {
                        console.log("Notification sent successfully:", resp.data.success);
                    })
                    .catch((error) => {
                        console.error("Failed to send notification:", error.response.data.error);
                    });
                setOpen(false)
            })
            .catch((error) => {
                // console.error("Submission failed with errors:", error);
                Swal.fire({
                    title: "Failed!",
                    text: error.response.data.error,
                    icon: "error",
                    showConfirmButton: true,
                });
                console.log(error);
            }).finally(() => {
                setLoading(false); // Reset submitting state
            });
    };

    const discountPercentage = () => {
        if (discount < 0 || discount > 100) {
            alert("Discount must be between 0 and 100");
            return;
        }
        const discountAmount = (cartTotal * discount) / 100;
        setContextDiscount(discountAmount);

        const recalculatedChargeAmount = calculateChargesWithDiscount(discountAmount);
        setRecalculatedCharges(recalculatedChargeAmount);
    }

    // Debug logging
    console.log('MpesaCheckoutDialog rendering', { mpesaEnabled, mpesaEnvConfigured, isMpesaAvailable, disabled });

    return (
        <Grid size={12}>
            <Button
                variant="contained"
                color={isMpesaAvailable ? "primary" : "warning"}
                sx={{
                    paddingY: "15px", 
                    flexGrow: "1",
                    backgroundColor: isMpesaAvailable ? "#1976d2" : "#ff9800",
                    '&:hover': {
                        backgroundColor: isMpesaAvailable ? "#1565c0" : "#f57c00",
                    }
                }}
                size="large"
                endIcon={<SmartphoneIcon />}
                onClick={handleClickOpen}
                disabled={disabled}
                fullWidth
            >
                {isMpesaAvailable 
                    ? (reactiveFinalTotal < 0 ? `REFUND ${formatCurrency(Math.abs(reactiveFinalTotal))}` : `MPESA ${formatCurrency(reactiveFinalTotal)}`)
                    : "MPESA (Not Configured)"
                }
            </Button>
            <Dialog
                fullWidth={true}
                maxWidth={"sm"}
                open={open}
                onClose={handleClose}
                aria-labelledby="alert-dialog-title"
                PaperProps={{
                    component: 'form',
                    onSubmit: handleSubmit,
                    'data-scan-focus-lock': 'true',
                }}
            >
                <DialogTitle id="alert-dialog-title">
                    {"MPesa Checkout"}
                </DialogTitle>
                <IconButton
                    aria-label="close"
                    onClick={handleClose}
                    sx={(theme) => ({
                        position: "absolute",
                        right: 8,
                        top: 8,
                        color: theme.palette.grey[500],
                    })}
                >
                    <CloseIcon />
                </IconButton>
                <DialogContent>
                    <TextField
                        fullWidth
                        variant="outlined"
                        label="MPesa Phone Number"
                        name="mpesa_phone"
                        value={mpesaPhone}
                        onChange={(e) => {
                            console.log('Phone number changed:', e.target.value);
                            console.log('Phone input event fired');
                            setMpesaPhone(e.target.value);
                        }}
                        placeholder="2547XXXXXXXX"
                        sx={{ mb: "1.5rem", input: { fontSize: '1.2rem' } }}
                        required
                        autoComplete="tel"
                        autoFocus
                        slotProps={{
                            inputLabel: {
                                shrink: true,
                            },
                        }}
                        inputProps={{
                            inputMode: "tel",
                        }}
                    />

                    <TextField
                        fullWidth
                        id="txtDiscount"
                        type="text"
                        name="discount"
                        label="Discount"
                        variant="outlined"
                        value={discountValue}
                        sx={{ mb: "1.5rem", input: { textAlign: "center", fontSize: '2rem' }, }}
                        onChange={handleDiscountChange}
                        onFocus={event => {
                            event.target.select();
                        }}
                        slotProps={{
                            inputLabel: {
                                shrink: true,
                            },
                        }}
                        InputProps={{
                            startAdornment: <InputAdornment position="start">{currencySymbol}</InputAdornment>,
                            endAdornment: (
                                <InputAdornment position="start">
                                    <IconButton color="primary" onClick={discountPercentage}>
                                        <PercentIcon fontSize="large"></PercentIcon>
                                    </IconButton>
                                </InputAdornment>
                            ),
                        }}
                        inputProps={{
                            inputMode: "decimal"
                        }}
                    />

                    <Grid container spacing={{ xs: 2, md: 1 }} mb={3}>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Payable Amount"
                                variant="outlined"
                                name="net_total"
                                value={formatCurrency((cartTotal - discount) + recalculatedCharges, false)}
                                sx={{input: { textAlign: "center", fontSize: '2rem' }, }}
                                slotProps={{
                                    input: {
                                        readOnly: true,
                                        style: { textAlign: 'center' },
                                        startAdornment: <InputAdornment position="start">{currencySymbol}</InputAdornment>,
                                    },
                                }}
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6}}>
                            <TextField
                                fullWidth
                                label="Amount"
                                variant="outlined"
                                name="amount_received"
                                type="text"
                                value={amountReceived}
                                onChange={(e) => {
                                    console.log('Mpesa amount changed:', e.target.value);
                                    console.log('Mpesa amount input event fired');
                                    setAmountReceived(e.target.value);
                                }}
                                sx={{input: { textAlign: "center", fontSize: '2rem' } }}
                                InputProps={{
                                    startAdornment: <InputAdornment position="start">{currencySymbol}</InputAdornment>,
                                }}
                                inputProps={{
                                    inputMode: "decimal"
                                }}
                            />
                        </Grid>
                    </Grid>

                    <TextField
                        fullWidth
                        variant="outlined"
                        label={'Note'}
                        name="note"
                        multiline
                        sx={{ mb: '2rem', }}
                        value={note}
                        onChange={(e) => {
                            console.log('Note changed:', e.target.value);
                            setNote(e.target.value);
                        }}
                        inputProps={{
                            inputMode: "text",
                        }}
                    />

                    <Grid container size={12} sx={{ mb: "1rem" }}>
                        <FormControlLabel
                            control={<Checkbox checked={openPrintDialog} onChange={(e) => setOpenPrintDialog(e.target.checked)} name="open_print_dialog" />}
                            label="Open Print Dialog"
                        />
                    </Grid>

                </DialogContent>
                <DialogActions>
                    <Button
                        variant="contained"
                        fullWidth
                        sx={{ paddingY: "15px", fontSize: "1.5rem" }}
                        type="submit"
                        disabled={!mpesaPhone || !amountReceived.trim() || (cartTotal < 0 && parseFloat(amountReceived) === 0) || (cartTotal < 0 && parseFloat(amountReceived) !== Math.abs((cartTotal - discount) + recalculatedCharges)) || (cartTotal >= 0 && (parseFloat(amountReceived) - ((cartTotal - discount) + recalculatedCharges)) < 0) || loading}
                    >
                        {loading ? 'Processing...' : cartTotal < 0 ? 'REFUND' : 'SEND STK PUSH'}
                    </Button>
                </DialogActions>
            </Dialog>
            <PrintReceiptModal
                open={showPrintModal}
                onClose={() => {
                    setShowPrintModal(false);
                    setReceiptData(null);
                }}
                receiptData={receiptData}
            />
        </Grid>
    );
}
