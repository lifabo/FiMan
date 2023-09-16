const navLinkElements = document.querySelectorAll(".nav-link");
const windowPathname = window.location.pathname;

navLinkElements.forEach(navLinkEl => {
    const navLinkPathname = new URL(navLinkEl.href).pathname;

    if (navLinkPathname === windowPathname) {
        navLinkEl.classList.add("active");
    }
})
