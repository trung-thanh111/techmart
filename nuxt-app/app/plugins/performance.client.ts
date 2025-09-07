export default defineNuxtPlugin(() => {
  // Tối ưu performance trong development
  if (process.env.NODE_ENV === 'development') {
    // Disable heavy console logs
    console.log = () => {}
    console.warn = () => {}
    
    // Tối ưu image loading
    const images = document.querySelectorAll('img')
    images.forEach(img => {
      if (img.loading !== 'lazy') {
        img.loading = 'lazy'
      }
    })
  }
})
