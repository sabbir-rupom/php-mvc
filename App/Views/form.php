<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Table Form</h1>
            <hr>
            <div id="alertMessage"></div>
            <form action="<?= baseUrl() . 'form/process' ?>" method="POST" id="submitForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputAmount">Amount</label>
                        <input type="number" class="form-control" id="inputAmount" name="amount" required="true" placeholder="enter number">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputReceipt">Receipt ID</label>
                        <input type="text" class="form-control text-only" id="inputReceipt" name="receipt_id" required="true" placeholder="enter text">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputBuyer">Buyer Name</label>
                        <input type="text" class="form-control text-s-num-only" id="inputBuyer" name="buyer" required="true" placeholder="enter name" maxlength="20">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputEmail">Buyer Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="buyer_email" required="true" placeholder="enter email">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="itemInputs">Items</label>
                        <div id="itemInputs">
                            <input type="text" class="form-control mb-1 text-only item" name="item[]" placeholder="enter item name">
                        </div>
                    </div>
                    <div class="form-group col-md-4 text-center pt-2">
                        <button type="button" class="btn btn-success add-item mt-4">Add More +</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputNote">Note</label>
                    <textarea class="form-control" name="note" id="inputNote" placeholder="enter text"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCity">City</label>
                        <input type="text" class="form-control text-s-only" id="inputCity" name="city" required="true" placeholder="e.g Dhaka">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPhone">Phone</label>
                        <input type="number" class="form-control" id="inputPhone" name="phone" title="please fill valid phone number" required="true" placeholder="e.g 8801*********">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEntry">Entry By</label>
                    <input type="number" class="form-control" id="inputEntry" name="entry_by" required="true" placeholder="number only">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
