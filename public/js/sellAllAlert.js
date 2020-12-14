function showAlert() {
    if(confirm("This action will sell all stocks and delete company from your wallet! Are you sure you want to continue?")) {
        document.forms['form-sell-all'].submit();
    }
    else {
        return false;
    }
}