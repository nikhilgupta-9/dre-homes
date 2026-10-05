<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=28 and is_active=1');
include('common/header.php');
$mortg_content = select_row('tbl_mortgage_content', 'is_active=1');

?>
<!--warpper-->
<div class="wrapper">
    <!--content-->
    <div class="content">
        <!--section-->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1>Mortgage Calculator</h1>
                            </div>
                        </div>
                    </div>
                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="<?= BASE_URL_IMG ?>mortgage/<?= $mortg_content['mortg_bg_img'] ?>"
                            data-scrollax="properties: { translateY: '30%' }">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--section-end-->
        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Mortgage Calculator</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <div class="scroll-content-wrap">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="post-container">
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3><?= $mortg_content['mortg_cont_heading'] ?></h3>
                                            <p><?= $mortg_content['mortg_cont_sub_heading'] ?></p>

                                        </div>
                                        <div class="boxed-content-item mt-c-box">
                                            <section class="wpo-payment-section-s2 section-padding">
                                                <div class="container">
                                                    <div class="wpo-payment-wrap border-design">
                                                        <div class="row justify-content-center align-items-stretch">
                                                            <div class="col col-lg-6">
                                                                <div class="payment-form-area">
                                                                    <div class="form-group">
                                                                        <label class="d-flex align-items-center justify-content-center">Purchase Price (<img src="<?= BASE_URL ?>images/symbol/currency-symbol.png" alt="AED" style="width: 15px;">):</label>
                                                                        <input type="number" value="50000"
                                                                            id="purchase_price">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Down Payment %:</label>
                                                                        <input type="number" value="20"
                                                                            id="down_payment">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Loan Duration (Years):</label>
                                                                        <input type="number" value="10" id="loan_term">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Interest Rate % (Annual):</label>
                                                                        <input type="number" value="5.5"
                                                                            id="interest_rate">
                                                                    </div>
                                                                    <div class="form-group mt-btn">
                                                                        <div class="row justify-content-center">
                                                                            <div class="col-12 col-lg-12 col-md-6 col-sm-12">
                                                                                <button type="button"
                                                                                    class="theme-btn theme-btn-me"
                                                                                    onclick="estimatePayment()">Estimate
                                                                                    Payment</button>
                                                                            </div>
                                                                            <div class="col-12 col-lg-12 col-md-6 col-sm-12">
                                                                                <button type="button"
                                                                                    class="loan-modal-open theme-btn theme-btn-ms">Apply
                                                                                    Loan</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Right Column with Charts -->
                                                            <div class="col col-lg-6 col-12 payment-result">
                                                                <div class="text-center">
                                                                    <div class="row align-items-center">
                                                                        <!-- Canvas Column -->
                                                                        <div class="col-12 col-lg-12 col-md-6 col-sm-12">
                                                                            <canvas id="combinedChart"></canvas>
                                                                        </div>
                                                            
                                                                        <!-- Text Breakdown Column -->
                                                                        <div class="col-12 col-lg-12 col-md-6 col-sm-12 text-start">
                                                                            <h4 class="pt-3">Loan Breakdown</h4>
                                                                            <div class="pt-2">
                                                                                <p class="mc-icon"><span style="color:#ffffff;">■</span>
                                            <strong>Down Payment:</strong>
                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol-w.png" alt="AED"
                                                style="width:15px;">
                                            <span id="down_payment_value">0.00</span>
                                        </p>
                                    
                                        <p class="mc-icon"><span style="color:#f3d7c4;">■</span>
                                            <strong>Loan Amount:</strong>
                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol-w.png" alt="AED"
                                                style="width:15px;">
                                            <span id="loan_amount_value">0.00</span>
                                        </p>
                                    
                                        <p class="mc-icon"><span style="color:#4a3b2a;">■</span>
                                            <strong>Annual Payment:</strong>
                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol-w.png" alt="AED"
                                                style="width:15px;">
                                            <span id="monthly_payment_value">0.00</span>
                                        </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add this at the bottom of the page before </body> -->
                        <script>
                            let combinedChart;

                            function estimatePayment() {
                                const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
                                const downPaymentPercent = parseFloat(document.getElementById('down_payment').value) || 0;
                                const loanTermYears = parseFloat(document.getElementById('loan_term').value) || 0;
                                const interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;

                                const downPaymentAmount = (downPaymentPercent / 100) * purchasePrice;
                                const loanAmount = purchasePrice - downPaymentAmount;
                                const annualInterestRate = interestRate / 100;
                                const totalYears = loanTermYears;
                                
                                let annualPayment = 0;
                                
                                if (annualInterestRate > 0) {
                                    const pow = Math.pow(1 + annualInterestRate, totalYears);
                                    annualPayment = loanAmount * ((annualInterestRate * pow) / (pow - 1));
                                } else {
                                    annualPayment = loanAmount / totalYears;
                                }


                                const formatAED = val => ` ${val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                                document.getElementById('down_payment_value').textContent = formatAED(downPaymentAmount);
                                document.getElementById('loan_amount_value').textContent = formatAED(loanAmount);
                                document.getElementById('monthly_payment_value').textContent = formatAED(annualPayment);

                                renderCombinedChart(downPaymentAmount, loanAmount, annualPayment);
                            }

                            function renderCombinedChart(down, loan, annualPay) {
                                const ctx = document.getElementById('combinedChart').getContext('2d');

                                if (combinedChart) {
                                    combinedChart.destroy();
                                }

                                combinedChart = new Chart(ctx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: ['Down Payment', 'Loan Amount', 'Annual Payment'],
                                        datasets: [{
                                            data: [down, loan, annualPay],
                                            backgroundColor: [
                                                '#ffffff',   
                                                '#f3d7c4',   
                                                '#4a3b2a'    
                                            ],
                                            borderWidth: 0
                                        }]
                                    },
                                    options: {
                                        cutout: '65%',
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                callbacks: {
                                                    label: context => {
                                                        const val = context.raw || 0;
                                                        return `${context.label}:  ${val.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            window.addEventListener('DOMContentLoaded', function () {
                                estimatePayment();
                            });
                        </script>

                    </div>
                    <div class="limit-box"></div>
                </div>
                <!--boxed-container end-->
                <!-- bank logos -->
                <div class="container">
                    <div class="boxed-container">
                        <div class="boxed-content ">
                            <div class="about-wrap boxed-content-item">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="about-title ab-hero">
                                            <h2>We work with more than 20 different lenders to get you the best
                                                mortgage
                                            </h2>
                                            <!-- <h4>It’s not just what we do—it’s how we do it that makes all the difference.</h4> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row mg-bank-logo text-center">
                                    <div class="col-md-3">
                                        <div class="bankL">
                                            <img src="images/mortgage-media/img-1.svg" alt="Bank 1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bankL">
                                            <img src="images/mortgage-media/img-2.svg" alt="Bank 2">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bankL">
                                            <img src="images/mortgage-media/img-3.svg" alt="Bank 3">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bankL">
                                            <img src="images/mortgage-media/img-4.svg" alt="Bank 4">
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <!-- bank logos-->
            </div>
            <!--main-content end-->
            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)">
                </div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)">
                </div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <!--content  end-->
    <?php include 'common/footer.php'; ?>