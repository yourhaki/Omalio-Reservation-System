let navbar = document.querySelector('.header .navbar')

document.querySelector('#menu-btn').onclick = () => {
  navbar.classList.toggle('active')
}

window.onscroll = () => {
  navbar.classList.remove('active')
}

document.querySelectorAll('.contact .row .faq .box h3').forEach((faqBox) => {
  faqBox.onclick = () => {
    faqBox.parentElement.classList.toggle('active')
  }
})

document.querySelectorAll('input[type="number"]').forEach((inputNumbmer) => {
  inputNumbmer.oninput = () => {
    if (inputNumbmer.value.length > inputNumbmer.maxLength)
      inputNumbmer.value = inputNumbmer.value.slice(0, inputNumbmer.maxLength)
  }
})

var swiper = new Swiper('.home-slider', {
  loop: true,
  effect: 'coverflow',
  spaceBetween: 30,
  grabCursor: true,
  coverflowEffect: {
    rotate: 50,
    stretch: 0,
    depth: 100,
    modifier: 1,
    slideShadows: false,
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
})

var swiper = new Swiper('.gallery-slider', {
  loop: true,
  effect: 'coverflow',
  slidesPerView: 'auto',
  centeredSlides: true,
  grabCursor: true,
  coverflowEffect: {
    rotate: 0,
    stretch: 0,
    depth: 100,
    modifier: 2,
    slideShadows: true,
  },
  pagination: {
    el: '.swiper-pagination',
  },
})

var swiper = new Swiper('.reviews-slider', {
  loop: true,
  slidesPerView: 'auto',
  grabCursor: true,
  spaceBetween: 30,
  pagination: {
    el: '.swiper-pagination',
  },
  breakpoints: {
    768: {
      slidesPerView: 1,
    },
    991: {
      slidesPerView: 2,
    },
  },
})

// Element references

const dropdownContent = document.getElementById("dropdownContent");
const adultCountSpan = document.getElementById("adultCount");
const childCountSpan = document.getElementById("childCount");
const closeDropdown = document.getElementById("closeDropdown");

// State
const guestState = {
  adults: 0,
  children: 0,
  max: 3,
};

// Function to update counts
function updateCounts() {
  adultCountSpan.textContent = guestState.adults;
  childCountSpan.textContent = guestState.children;
  guestButton.textContent = ` ${guestState.adults} Adults, ${guestState.children} Children`;

  document.querySelectorAll('.decrement[data-type="adults"]').forEach((btn) => {
    btn.disabled = guestState.adults === 0;
  });

  document.querySelectorAll('.increment[data-type="adults"]').forEach((btn) => {
    btn.disabled = guestState.adults === guestState.max;
  });

  document.querySelectorAll('.decrement[data-type="children"]').forEach((btn) => {
    btn.disabled = guestState.children === 0;
  });

  document.querySelectorAll('.increment[data-type="children"]').forEach((btn) => {
    btn.disabled = guestState.children === guestState.max;
  });
}

// Event listeners
guestButton.addEventListener("click", () => {
  dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
});

closeDropdown.addEventListener("click", () => {
  dropdownContent.style.display = "none";
});

document.querySelectorAll(".increment").forEach((button) => {
  button.addEventListener("click", (e) => {
    const type = e.target.dataset.type;
    if (guestState[type] < guestState.max) {
      guestState[type]++;
      updateCounts();
    }
  });
});

document.querySelectorAll(".decrement").forEach((button) => {
  button.addEventListener("click", (e) => {
    const type = e.target.dataset.type;
    if (guestState[type] > 0) {
      guestState[type]--;
      updateCounts();
    }
  });
});

// Initialize
updateCounts();

