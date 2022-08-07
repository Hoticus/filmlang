module.exports = {
  content: ["./assets/**/*.{js,jsx}", "./templates/**/*.twig"],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
