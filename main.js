document.addEventListener("DOMContentLoaded", () => {
  // --- NAVBAR TOGGLE ---
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");
  const links = document.querySelectorAll(".nav-links a");
  hamburger.addEventListener("click", () => navLinks.classList.toggle("active"));
  links.forEach(link => link.addEventListener("click", () => navLinks.classList.remove("active")));

  // --- CART ELEMENTS ---
  const addButtons = document.querySelectorAll(".add");
  const cartBtn = document.getElementById("cartBtn");
  const cartPopup = document.getElementById("cartPopup");
  const checkoutPopup = document.getElementById("checkoutPopup");
  const closeCart = document.getElementById("closeCart");
  const checkoutBtn = document.getElementById("checkoutBtn");
  const closeCheckout = document.getElementById("closeCheckout");
  const checkoutForm = document.getElementById("checkoutForm");
  const cartItemsList = document.getElementById("cartItems");
  const cartTotal = document.getElementById("cartTotal");

  // --- CART BADGE & TOAST ---
  const badge = document.createElement("span");
  badge.id = "cartBadge";
  badge.textContent = "0";
  cartBtn.appendChild(badge);

  const toast = document.createElement("div");
  toast.id = "toast";
  document.body.appendChild(toast);

  let cart = [];

  // --- ADD TO CART ---
  addButtons.forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const card = e.target.closest(".card");
      const name = card.querySelector("h3").textContent;
      const price = parseInt(card.querySelector("h4").textContent.replace("₹", ""));
      const imgSrc = card.querySelector("img").src;
      const existingItem = cart.find(item => item.name === name);
      if (existingItem) existingItem.qty++;
      else cart.push({ name, price, qty: 1, img: imgSrc });
      updateCart();
      showToast(`${name} added to cart`);
    });
  });

  // --- UPDATE CART ---
  function updateCart() {
    cartItemsList.innerHTML = "";
    let total = 0;
    let itemCount = 0;

    cart.forEach((item, index) => {
      total += item.price * item.qty;
      itemCount += item.qty;

      const li = document.createElement("li");
      li.innerHTML = `
        <div class="cart-item">
          <img src="${item.img}" alt="${item.name}">
          <div class="cart-details">
            <strong>${item.name}</strong><br>
            ₹${item.price} x ${item.qty} = ₹${item.price * item.qty}
            <div class="cart-actions">
              <button class="decrease" data-index="${index}">−</button>
              <button class="increase" data-index="${index}">+</button>
              <button class="delete" data-index="${index}">🗑️</button>
            </div>
          </div>
        </div>
      `;
      cartItemsList.appendChild(li);
    });

    cartTotal.textContent = total;
    badge.textContent = itemCount;

    // Quantity Controls
    document.querySelectorAll(".increase").forEach(btn =>
      btn.addEventListener("click", e => {
        const i = e.target.dataset.index;
        cart[i].qty++;
        updateCart();
      })
    );
    document.querySelectorAll(".decrease").forEach(btn =>
      btn.addEventListener("click", e => {
        const i = e.target.dataset.index;
        if (cart[i].qty > 1) cart[i].qty--;
        else cart.splice(i, 1);
        updateCart();
      })
    );
    document.querySelectorAll(".delete").forEach(btn =>
      btn.addEventListener("click", e => {
        const i = e.target.dataset.index;
        cart.splice(i, 1);
        updateCart();
      })
    );
  }

  // --- TOAST POPUP ---
  function showToast(message) {
    toast.textContent = message;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2000);
  }

  // --- CART POPUP EVENTS ---
  cartBtn.addEventListener("click", () => cartPopup.classList.remove("hidden"));
  closeCart.addEventListener("click", () => cartPopup.classList.add("hidden"));
  checkoutBtn.addEventListener("click", () => {
    cartPopup.classList.add("hidden");
    checkoutPopup.classList.remove("hidden");
  });
  closeCheckout.addEventListener("click", () => checkoutPopup.classList.add("hidden"));

  // --- CHECKOUT VALIDATION & PINCODE FETCH ---
  const pincodeInput = document.getElementById("pincode");
  const cityInput = document.getElementById("city");
  const stateInput = document.getElementById("state");
  const mobileInput = document.getElementById("mobile");
  const emailInput = document.getElementById("email");

  // Spinner for pincode fetch
  const spinner = document.createElement("div");
  spinner.id = "spinner";
  spinner.textContent = "Fetching location...";
  spinner.style.display = "none";
  spinner.style.color = "#FF6F61";
  spinner.style.fontSize = "0.9rem";
  pincodeInput.insertAdjacentElement("afterend", spinner);

  // Only numbers in mobile field
  mobileInput.addEventListener("input", () => {
    mobileInput.value = mobileInput.value.replace(/\D/g, "").slice(0, 10);
  });

  // Auto-fetch City/State from Pincode
  pincodeInput.addEventListener("input", () => {
    const pincode = pincodeInput.value.trim();
    if (pincode.length === 6) {
      spinner.style.display = "block";
      fetch(`https://api.postalpincode.in/pincode/${pincode}`)
        .then(res => res.json())
        .then(data => {
          spinner.style.display = "none";
          if (data[0].Status === "Success") {
            const post = data[0].PostOffice[0];
            cityInput.value = post.District;
            stateInput.value = post.State;
          } else {
            cityInput.value = "";
            stateInput.value = "";
            showToast("Invalid Pincode");
          }
        })
        .catch(() => {
          spinner.style.display = "none";
          showToast("Error fetching location");
        });
    } else {
      cityInput.value = "";
      stateInput.value = "";
      spinner.style.display = "none";
    }
  });

  // --- CHECKOUT SUBMIT (Final Single Version) ---
  checkoutForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      showToast("Enter a valid email address");
      return;
    }

    if (mobileInput.value.length !== 10) {
      showToast("Enter a valid 10-digit mobile number");
      return;
    }

    if (!pincodeInput.value || !cityInput.value || !stateInput.value) {
      showToast("Please enter a valid pincode");
      return;
    }

    const order = {
      name: document.getElementById("name").value,
      mobile: mobileInput.value,
      email,
      address: document.getElementById("addressLine").value,
      pincode: pincodeInput.value,
      city: cityInput.value,
      state: stateInput.value,
      cart,
    };

    // Send to PHP file to save order
    fetch("save_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(order)
    })
      .then(res => res.text())
      .then(data => {
        showToast("Order placed successfully!");
        cart = [];
        updateCart();
        checkoutForm.reset();
        checkoutPopup.classList.add("hidden");
      })
      .catch(() => showToast("Error saving order."));
  });
});
