<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Edit item </title>
        <base href="<?= $web_root ?>">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">

    </head>
    <header>
        <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Add item 🛒 </div> </nav>
         </header>   

    </header>
    <body >
        <main class="container mb-3" >
            <br>
            <div class="container fw-bold ">
                <div class= "card bg-dark mb-3  border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Basic Information</div>
                    <div class="card-body text-light border-0 ">
                        <form>
                            <div class="mb-3">
                                <label for="itemtitle" class="form-label">Item Title *</label>
                                <input type="text" class="form-label d-block p-2 text-bg-dark" id="itemtitle" placeholder="Ex: iPhone 13 Pro Max 256GB" >
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-label d-block p-2 text-bg-dark" id="itemtitle" placeholder="Describe your item in detail" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="saledays" class="form-label">Sale Duration (days)*</label>
                                <input type="nomber" class="form-label d-block p-2 text-bg-dark" id="itemtitle" placeholder="7">
                            </div>
                        </form>
                    </div>    
                </div>
            </div>  
            <div class="container fw-bold">
                <div class="card bg-dark mb-3  border-0 overflow-hidden" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Sale Type</div>
                    <div class="card-body text-white border-0 ">
                        <div class="card bg-dark mb-3  border-1 border-primary overflow-hidden" >
                            <div class="card-header border-1 border-primary bg-primary text-white" style="--bs-bg-opacity: .1">Option 1:Auction</div>
                            <div class="card-body  border-0  ">
                                <form>
                                    <div class="mb-3">
                                        <label for="startingbid" class="form-label text-white">Starting Bid</label>
                                        <input type="text" class="form-label d-block p-2 text-bg-dar" id="startingbid" placeholder="e.g.,50.00">
                                    </div>
                                    <div class="mb-3">
                                        <label for="purchaseprice" class="form-label text-white">Instant Purchase Price</label><a class="text-light">(optional)</a>
                                        <input type="text" class="form-label d-block p-2 text-bg-dar" id="purchaseprice" placeholder="e.g.,200">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card bg-dark mb-3 border-success overflow-hidden">
                            <div class="card-header border-1 border-success bg-succes text-success" style="--bs-bg-opacity: .1"> Option 2: Direct Sale</div>
                            <div class="card-body border-0">
                                <form class="justify">
                                    <div class="mb-3">
                                        <label for="saleprice" class="form-label text-white">Sale Price</label>
                                        <input type="text" class="form-label d-block p-2 text-bg-dar" id="saleprice" placeholder="e.g.,150.00">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>

            </div>  
        </main>
    </body>   


</html>