/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/auth/*.blade.php',
    './resources/views/website/*.blade.php',
    './resources/views/website/includes/*.blade.php',
    './resources/views/website/pages/*.blade.php',
    './resources/views/website/pages/**/*.blade.php',
    './resources/views/errors/*.blade.php',
    './resources/views/layouts/*.blade.php',
    './resources/views/vendor/**/*.blade.php',
    './resources/views/share/**/*.blade.php',
    './resources/views/templates/**/*.blade.php',
    './resources/views/templates/*.blade.php',
    './resources/views/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

