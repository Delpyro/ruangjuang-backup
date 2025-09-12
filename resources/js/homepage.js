import AOS from 'aos';
import 'aos/dist/aos.css';

AOS.init({
    duration: 800,
    once: true,
    offset: 50,
});

const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
const loadingScreen = document.getElementById('loading-screen');
const progressBar = document.getElementById('progress-bar');
const navLinks = document.querySelectorAll('.nav-link, button.w-full');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    mobileMenu.classList.toggle('open');
});

document.addEventListener('click', (e) => {
    if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('open');
    }
});

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('open');
    });
});

window.addEventListener('load', () => {
    progressBar.style.width = '100%';
    setTimeout(() => {
        loadingScreen.classList.add('hidden');
    }, 2000);
});