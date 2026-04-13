<style>
  .font-playfair { font-family: 'Playfair Display', serif; }
  .upper-shell {
    background-image: url('{{ asset('assets/homeBG.png') }}');
    background-position: center top;
    background-size: cover;
    background-repeat: no-repeat;
  }
  .upper-nav-link {
    position: relative;
    font-size: 15px;
    letter-spacing: .03em;
    color: rgba(255, 255, 255, 0.9);
    transition: color .2s ease;
  }
  .upper-nav-link:hover { color: #fff; }
  .upper-nav-link.is-active::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -8px;
    border-bottom: 2px solid #ffa006;
  }
  .upper-auth-segment {
    border: 2px solid #ffa006;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 18px rgba(226, 142, 8, 0.34);
  }
  .upper-login-btn {
    min-width: 138px;
    color: #ffffff;
    background: rgba(43, 3, 82, 0);
    transition: background-color .2s ease;
  }
  .upper-login-btn:hover { background: #ffa006; }
  .upper-signup-btn {
    min-width: 138px;
    color: #ffffff;
    background: rgba(43, 3, 82, 0);
    transition: background-color .2s ease;
  }
  .upper-signup-btn:hover { background: #ffa006; }
  .upper-shell .language-switcher .language-toggle {
    border: 2px solid #ffa006;
    background: rgba(42, 3, 82, 0.5);
    color: #ffffff;
    padding: 18px;
    border-radius: 8px;
    box-shadow: 0 0 18px rgba(226, 142, 8, 0.28);
  }
  .upper-shell .language-switcher .language-toggle:hover {
    background: #ffa006;
    color: #ffffff;
  }
  .upper-shell .language-switcher .language-toggle svg {
    color: rgba(255, 255, 255, 0.88);
  }
  .upper-shell .language-switcher .language-menu {
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(25, 7, 42, 0.95);
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
    border-radius: 12px;
  }
  .upper-shell .language-switcher .language-option {
    color: rgba(255, 255, 255, 0.92);
  }
  .upper-shell .language-switcher .language-option:hover {
    background: rgba(255, 160, 6, 0.2);
    color: #ffffff;
  }
  .upper-shell .language-switcher .language-option.active {
    background: rgba(255, 160, 6, 0.24);
    color: #ffffff;
  }
  .upper-shell .language-switcher .language-option.active .text-pink-600 {
    color: #ffa006;
  }
  @media (max-width: 768px) {
    .upper-shell .language-switcher .language-toggle {
      padding: 10px;
    }
  }
</style>
