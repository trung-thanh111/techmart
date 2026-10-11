export default {
  content: [
    "./app/**/*.{vue,js,ts}",
  ],
  theme: {
    extend: {
      fontSize: {
        'md': ['0.875rem', { lineHeight: '1.25rem' }],
      },
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        'inter': ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}