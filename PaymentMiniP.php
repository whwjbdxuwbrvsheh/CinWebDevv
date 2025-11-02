<?php
session_start();
require 'ConnMiniP.php';

// -- (Original logic preserved) --
// Check if user is logged in
if (isset($_SESSION['email_address'])) {

} else {
    echo "You are not logged in. <a href='LoginMiniP.php'>Login here</a>";
    exit();
}

// Get booking details from session
$booking_id = $_SESSION['booking_id'];
$selected_seats = $_SESSION['selected_seats'];

// Calculate total amount
$total_amount = 0;
foreach ($selected_seats as $seat_id) {
    $seat = GetSeatByID($seat_id);
    $total_amount = $total_amount + $seat['price'];
}

// Check if payment was successful
if (isset($_GET['success'])) {
    $payment_success = true;
} else {
    $payment_success = false;
}

// --- Defensive fix for undefined array key warning (minimal, safe) ---
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
// --------------------------------------------------------------------

if ($_POST) {
    // use $payment_method as filled by POST (or null)
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if ($action == 'select') {
        $user_choice = $payment_method;
    }
    
    if ($action == 'pay') {
        if ($payment_method == 'Bitcoin' || $payment_method == 'Monero') {
            $wallet_address = $_POST['wallet_address'];
            $crypto_amount = $_POST['crypto_amount'];
        } else {
            $card_number = $_POST['card_number'];
            $card_name = $_POST['card_name'];
            $expiry_date = $_POST['expiry_date'];
            $cvv = $_POST['cvv'];
        }
        
        if (CreatePayment($booking_id, $payment_method, $card_number, $card_name, $expiry_date, $cvv, $wallet_address, $crypto_amount, $total_amount, 'Completed')) {
            foreach ($selected_seats as $seat_id) {
                $seat = GetSeatByID($seat_id);
                CreateTicket($booking_id, $seat_id, $seat['seat_type'], $seat['price']);
            }
            
            header("Location: PaymentMiniP.php?success=1");
            exit();
        }
    }
}

// Get payment methods from database
$crypto_options = GetPaymentMethodsByType('crypto');
$card_options = GetPaymentMethodsByType('card');

// Check user choice
if (isset($user_choice)) {
    $current_choice = $user_choice;
} else {
    $current_choice = '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ZTAVerse • Payment</title>

  <!-- Tailwind CDN (development friendly) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700&family=Saira+Semi+Condensed:wght@400;700&display=swap" rel="stylesheet">

  <style>
  /* ---------------------------
     Design Tokens (primitive + semantic)
     --------------------------- */
  :root{
    /* primitives */
    --black-900: #0a0a0a;
    --black-700: #1a0a0a;
    --white: #ffffff;
    --red-600: #ff1a1a;
    --red-500: #ff4d4d;
    --muted-600: rgba(255,255,255,0.75);
    --glass-01: rgba(20,20,20,0.7);

    /* semantic */
    --bg-gradient: linear-gradient(135deg, var(--black-900), #1a0000, #330000);
    --card-bg: var(--glass-01);
    --text-main: var(--white);
    --accent: var(--red-600);
    --muted: var(--muted-600);

    /* motion tokens */
    --ease-quick: cubic-bezier(.2,.9,.3,1);
    --ease-standard: cubic-bezier(.16,.84,.44,1);
    --dur-xs: 120ms;
    --dur-sm: 200ms;
    --dur-md: 320ms;
    --dur-lg: 520ms;
  }

  /* Light-mode tokens (swapped) */
  body.light-mode {
    --bg-gradient: linear-gradient(135deg, #fff0f0, #ffeaea);
    --card-bg: linear-gradient(145deg, #ffcccc, #ff9999);
    --text-main: #111111;
    --accent: #ff4d4d;
    --muted: rgba(17,17,17,0.65);
  }

  /* ---------------------------
     Base page styles
     --------------------------- */
  html,body{height:100%;}
  body{
    font-family: 'Saira Semi Condensed', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    margin:0;
    color:var(--text-main);
    background: var(--bg-gradient);
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    transition: background 0.45s var(--ease-standard), color 0.45s var(--ease-standard);
    min-height:100vh;
    display:flex;
    align-items:flex-start;
    justify-content:center;
    padding:56px 20px 80px;
  }

  /* container */
  .container {
    width:100%;
    max-width:980px;
    border-radius:14px;
    padding:28px;
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.04);
  }

  /* NAVBAR (matches IndexMiniP) */
  .nav {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-bottom:20px;
  }
  .brand {
    display:flex;
    align-items:center;
    gap:12px;
  }
  .brand h1 {
    font-size:1.6rem;
    color:var(--accent);
    margin:0;
    letter-spacing:0.6px;
    text-shadow: 0 0 14px rgba(255,77,77,0.12);
  }
  .nav .controls {
    display:flex;
    gap:8px;
    align-items:center;
  }

  /* glass card */
  .glass {
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius:12px;
    padding:18px;
    border:1px solid rgba(255,255,255,0.06);
    box-shadow: 0 8px 30px rgba(255,0,0,0.06);
    transition: box-shadow var(--dur-sm) var(--ease-quick), transform var(--dur-sm) var(--ease-quick);
  }
  .glass:focus-within{ box-shadow: 0 10px 50px rgba(255,0,0,0.12); transform: translateY(-3px); }

  /* grid layout */
  .grid-2 {
    display:grid;
    grid-template-columns: 1fr 380px;
    gap:18px;
  }
  @media (max-width:1024px) { .grid-2 { grid-template-columns: 1fr; } }

  /* payment methods area */
  .methods {
    display:flex;
    flex-direction:column;
    gap:12px;
  }
  .method-list{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap:12px;
  }

  /* method card */
  .method {
    display:flex;
    gap:12px;
    align-items:center;
    padding:12px;
    border-radius:10px;
    cursor:pointer;
    border:1px solid rgba(255,255,255,0.04);
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    transition: transform var(--dur-sm) var(--ease-standard), box-shadow var(--dur-sm) var(--ease-standard), border-color var(--dur-sm) var(--ease-standard);
    outline: none;
  }
  .method:hover, .method:focus {
    transform: translateY(-6px);
    box-shadow: 0 14px 40px rgba(255,0,0,0.12);
    border-color: rgba(255,77,77,0.18);
  }
  .method .logo {
    width:44px;
    height:44px;
    border-radius:8px;
    background: rgba(0,0,0,0.35);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    color:var(--text-main);
    box-shadow: inset 0 -6px 10px rgba(0,0,0,0.2);
  }
  .method input[type="radio"] {
    appearance:none;
    width:20px;height:20px;border-radius:999px;border:2px solid rgba(255,255,255,0.12);
    display:inline-block;flex-shrink:0;margin-left:auto;
    position:relative;
    transition: all var(--dur-xs) var(--ease-quick);
  }
  .method[data-selected="true"]{
    border-color: var(--accent);
    box-shadow: 0 10px 30px rgba(255,0,0,0.08);
  }
  .method[data-selected="true"] input[type="radio"]{
    background: var(--accent);
    border-color: var(--accent);
  }

  /* right column: summary & actions */
  .summary {
    display:flex;
    flex-direction:column;
    gap:12px;
  }
  .price {
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-weight:700;
    font-size:1.1rem;
  }

  /* input fields */
  .field {
    display:flex;
    flex-direction:column;
    gap:8px;
  }
  .text, input[type="text"], input[type="tel"], input[type="number"] {
    padding:12px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.02);
    color:var(--text-main);
    transition: box-shadow var(--dur-sm) var(--ease-standard), border-color var(--dur-sm) var(--ease-standard);
  }
  .text:focus, input[type="text"]:focus {
    box-shadow: 0 6px 22px rgba(255,0,0,0.08);
    border-color: var(--accent);
    outline: none;
  }

  /* main CTA */
  .btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:12px 18px;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
    border:none;
    background: linear-gradient(90deg, #ffffff, var(--accent));
    color:#000;
    box-shadow: 0 10px 30px rgba(255,0,0,0.10);
    transition: transform var(--dur-xs) var(--ease-quick), box-shadow var(--dur-sm) var(--ease-standard);
  }
  .btn:active{ transform: translateY(1px) scale(0.997); }
  .btn[disabled]{ opacity:0.5; cursor:not-allowed; transform:none; box-shadow:none; }

  /* subtle motion - float posters (reused feel) */
  @keyframes floatSmall {
    0% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0); }
  }

  /* success state */
  .success {
    display:flex;
    align-items:center;
    gap:12px;
    padding:18px;
    border-radius:12px;
    background: linear-gradient(90deg, rgba(0,200,80,0.08), rgba(0,200,80,0.04));
    border: 1px solid rgba(0,200,80,0.12);
    color: #bfffc4;
  }

  /* accessibility focus ring fallback */
  :focus { outline: 3px solid rgba(255,77,77,0.14); outline-offset:4px; }

  /* small utility */
  .muted { color:var(--muted); font-size:0.95rem; }
  </style>
</head>
<body>

  <div class="container" role="main" aria-labelledby="payment-heading">
    <!-- NAV -->
    <div class="nav">
      <div class="brand">
        <h1>ZTAVerse</h1>
        <div class="muted">Payment</div>
      </div>

      <div class="controls">
        <div class="muted">Welcome <?php echo htmlspecialchars($_SESSION['email_address']); ?></div>
        <a href="LogoutMiniP.php" class="muted" style="text-decoration:underline;margin-left:8px">Logout</a>

        <!-- Light/Dark Toggle -->
        <button id="modeToggle" aria-pressed="false" title="Toggle light / dark" class="glass" style="margin-left:12px; padding:8px 12px;">
          <span id="modeIcon">☀ Light</span>
        </button>
      </div>
    </div>

    <!-- Main content grid -->
    <div class="grid-2" role="region" aria-label="Payment selection area">
      <!-- Left: methods & form -->
      <section class="glass" aria-labelledby="methods-heading">
        <h2 id="methods-heading" class="text-lg font-bold" style="margin-bottom:12px">Select Payment Method</h2>

        <?php if ($payment_success): ?>
          <div class="success" role="status" aria-live="polite">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M20 6L9 17l-5-5" stroke="#0f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div>
              <div class="font-bold">Payment Successful</div>
              <div class="muted">Your booking is confirmed. <a href="ReviewMiniP.php?booking_id=<?php echo $booking_id; ?>" style="text-decoration:underline;color:var(--accent)">Continue to Review</a></div>
            </div>
          </div>
        <?php else: ?>

          <form id="paymentForm" method="post" novalidate>
            <input type="hidden" name="action" id="formAction" value="<?php echo $current_choice == '' ? 'select' : 'pay'; ?>">

            <!-- Method lists -->
            <div class="methods" aria-hidden="false">
              <div>
                <div class="muted" style="margin-bottom:8px">Cryptocurrencies</div>
                <div class="method-list" role="list" aria-label="Cryptocurrency options">
                  <?php foreach ($crypto_options as $crypto): 
                    $name = htmlspecialchars($crypto['method_name']);
                  ?>
                    <label class="method" role="listitem" tabindex="0" data-value="<?php echo $name; ?>" data-type="crypto">
                      <div class="logo" aria-hidden="true"><?php echo strtoupper(substr($name,0,2)); ?></div>
                      <div>
                        <div style="font-weight:700"><?php echo $name; ?></div>
                        <div class="muted">Fast • Borderless</div>
                      </div>
                      <input type="radio" name="payment_method" value="<?php echo $name; ?>" class="sr-only" aria-hidden="true">
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div style="margin-top:10px">
                <div class="muted" style="margin-bottom:8px">Credit Cards</div>
                <div class="method-list" role="list" aria-label="Credit card options">
                  <?php foreach ($card_options as $card): 
                    $cname = htmlspecialchars($card['method_name']);
                  ?>
                    <label class="method" role="listitem" tabindex="0" data-value="<?php echo $cname; ?>" data-type="card">
                      <div class="logo" aria-hidden="true"><?php echo strtoupper(substr($cname,0,2)); ?></div>
                      <div>
                        <div style="font-weight:700"><?php echo $cname; ?></div>
                        <div class="muted">Secure • Verified</div>
                      </div>
                      <input type="radio" name="payment_method" value="<?php echo $cname; ?>" class="sr-only" aria-hidden="true">
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <!-- Dynamic fields area -->
            <div id="dynamicFields" style="margin-top:14px"></div>

            <!-- Select or Pay CTA -->
            <div style="display:flex; gap:12px; margin-top:18px; align-items:center;">
              <button type="submit" id="primaryBtn" class="btn" aria-live="polite">
                <span id="primaryBtnLabel">Select Payment Method</span>
              </button>

              <button type="button" id="cancelBtn" class="glass" style="padding:10px 12px;" aria-hidden="false">Cancel</button>

              <div style="margin-left:auto;" class="muted">Total: <strong>RM<?php echo number_format($total_amount,2); ?></strong></div>
            </div>
          </form>
        <?php endif; ?>
      </section>

      <!-- Right: Summary / ticket -->
      <aside class="glass summary" aria-labelledby="summary-heading">
        <h3 id="summary-heading" class="font-bold">Booking Summary</h3>

        <div class="price">
          <div class="muted">Seats</div>
          <div class="muted"><?php echo count($selected_seats); ?> seats</div>
        </div>

        <div class="price">
          <div class="muted">Total</div>
          <div style="font-size:1.4rem; color:var(--text-main);">RM<?php echo number_format($total_amount,2); ?></div>
        </div>

        <div style="margin-top:8px" class="muted">Payment is processed securely. We never store CVV.</div>

        <!-- quick hints -->
        <div style="margin-top:12px; display:flex; flex-direction:column; gap:8px;">
          <div class="muted"><strong>Quick tips</strong></div>
          <ul class="muted" style="margin-left:14px; list-style:disc;">
            <li>Use card for instant confirmation.</li>
            <li>Crypto payments may require manual confirmation.</li>
            <li>Need help? <a href="#" style="color:var(--accent); text-decoration:underline">Contact us</a></li>
          </ul>
        </div>
      </aside>
    </div>
  </div>

  <script>
  /**
   * UI behaviour:
   * - method card selection (keyboard + click)
   * - dynamic field rendering for crypto vs card
   * - primary button toggles between "Select Payment Method" and "Pay with X"
   * - light/dark (light-mode class) replicated like IndexMiniP
   * - accessible: role attributes, focusable cards, keyboard handlers
   *
   * Motion choreography:
   * - hover: translateY -6px => 200ms ease-standard
   * - select: border-color => immediate visual feedback
   *
   * Durations / easings are referenced from CSS tokens.
   */

  (function(){
    // Elements
    const methodEls = Array.from(document.querySelectorAll('.method'));
    const dynamicArea = document.getElementById('dynamicFields');
    const form = document.getElementById('paymentForm');
    const primaryBtn = document.getElementById('primaryBtn');
    const primaryLabel = document.getElementById('primaryBtnLabel');
    const formAction = document.getElementById('formAction');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const cancelBtn = document.getElementById('cancelBtn');

    // Utility: set selected visual
    function clearSelection(){
      methodEls.forEach(m => {
        m.dataset.selected = 'false';
        const inp = m.querySelector('input[type="radio"]');
        if (inp) inp.checked = false;
      });
    }

    function setSelection(el){
      clearSelection();
      el.dataset.selected = 'true';
      const val = el.dataset.value;
      const inp = el.querySelector('input[type="radio"]');
      if (inp) inp.checked = true;
      // update dynamic fields
      renderFields(el.dataset.type, val);
      // Toggle action to "pay" so server expects pay operation.
      formAction.value = 'pay';
      primaryLabel.textContent = `Pay with ${val}`;
    }

    // Render dynamic fields for card vs crypto
    function renderFields(type, name){
      // Accessibility: announce
      dynamicArea.innerHTML = ''; // clear

      if (!type) return;
      if (type === 'crypto') {
        dynamicArea.innerHTML = `
          <div class="field">
            <label class="muted">Wallet Address <span aria-hidden="true">*</span></label>
            <input name="wallet_address" type="text" class="text" placeholder="Enter wallet address" required aria-required="true">
          </div>
          <div class="field" style="margin-top:8px;">
            <label class="muted">Amount (RM) <span aria-hidden="true">*</span></label>
            <input name="crypto_amount" type="text" class="text" placeholder="Calculated amount" value="${parseFloat(<?php echo json_encode($total_amount); ?>).toFixed(2)}" required aria-required="true" readonly>
          </div>
        `;
      } else {
        dynamicArea.innerHTML = `
          <div class="field">
            <label class="muted">Card Number</label>
            <input name="card_number" type="text" inputmode="numeric" class="text" placeholder="4242 4242 4242 4242" required aria-required="true" />
          </div>
          <div class="field" style="display:flex; gap:8px; margin-top:8px;">
            <div style="flex:1;">
              <label class="muted">Cardholder Name</label>
              <input name="card_name" type="text" class="text" placeholder="Name on card" required />
            </div>
            <div style="width:140px;">
              <label class="muted">Expiry (MM/YY)</label>
              <input name="expiry_date" type="text" class="text" placeholder="MM/YY" required />
            </div>
            <div style="width:100px;">
              <label class="muted">CVV</label>
              <input name="cvv" type="text" class="text" placeholder="123" inputmode="numeric" required />
            </div>
          </div>
        `;
      }
    }

    // click + keyboard select
    methodEls.forEach(el => {
      el.dataset.selected = 'false';
      el.addEventListener('click', () => setSelection(el));
      el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          setSelection(el);
        } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
          e.preventDefault();
          const idx = methodEls.indexOf(el);
          const next = methodEls[(idx+1)%methodEls.length];
          next.focus();
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
          e.preventDefault();
          const idx = methodEls.indexOf(el);
          const prev = methodEls[(idx-1+methodEls.length)%methodEls.length];
          prev.focus();
        }
      });
    });

    // Form submit handler (client-side validation before POST)
    form && form.addEventListener('submit', function(e){
      // If action is 'select' it means user clicked select without choosing
      const selected = methodEls.find(m => m.dataset.selected === 'true');
      if (!selected && formAction.value !== 'pay') {
        e.preventDefault();
        alert('Please choose a payment method first.');
        return;
      }
      // If pay, do minimal validation for required fields
      if (formAction.value === 'pay') {
        // ensure required inputs exist and have values
        const required = Array.from(form.querySelectorAll('[required]'));
        for (let r of required){
          if (!r.value || r.value.trim() === '') {
            e.preventDefault();
            r.focus();
            alert('Please complete the highlighted field before paying.');
            return;
          }
        }
        // disable primary button to prevent double-submit; visual loading
        primaryBtn.disabled = true;
        primaryBtn.style.opacity = '0.8';
        primaryLabel.textContent = 'Processing...';
      } else {
        // selection action: convert to pay mode client-side so user sees fields
        e.preventDefault();
        // find checked radio and programmatically choose it (some browsers might not set it)
        const firstChecked = form.querySelector('input[name="payment_method"]:checked');
        if (firstChecked) {
          const cardEl = methodEls.find(m => m.dataset.value === firstChecked.value);
          if (cardEl) setSelection(cardEl);
        } else {
          alert('Please select a payment method before proceeding.');
        }
      }
    });

    // Cancel -> navigate back to Cinema or Index page (non-destructive)
    cancelBtn && cancelBtn.addEventListener('click', function(){
      window.location.href = 'IndexMiniP.php';
    });

    // Light / Dark toggle - consistent with IndexMiniP
    function applyTheme(theme){
      if (theme === 'light') {
        document.body.classList.add('light-mode');
        modeIcon.textContent = '🌙 Dark';
        modeToggle.setAttribute('aria-pressed','true');
      } else {
        document.body.classList.remove('light-mode');
        modeIcon.textContent = '☀ Light';
        modeToggle.setAttribute('aria-pressed','false');
      }
      localStorage.setItem('ztaverse-theme', theme);
    }

    // init theme from local storage or default (dark)
    const saved = localStorage.getItem('ztaverse-theme') || 'dark';
    applyTheme(saved);

    modeToggle.addEventListener('click', function(){
      const isLight = document.body.classList.contains('light-mode');
      applyTheme(isLight ? 'dark' : 'light');
    });

    // If user arrived with a prior selection (server-side), highlight matching card
    (function highlightServerChoice(){
      const serverChoice = <?php echo json_encode($current_choice); ?>;
      if (serverChoice) {
        const el = methodEls.find(m => m.dataset.value === serverChoice);
        if (el) setSelection(el);
        // ensure button now says Pay...
        primaryLabel.textContent = `Pay with ${serverChoice}`;
        formAction.value = 'pay';
      }
    })();

  })();
  </script>

  <!-- small performance comment: keep scripts at bottom  -->
  <!-- Performance: minimal JS, no external heavy libs, Tailwind CDN used for utility classes only -->
</body>
</html>






