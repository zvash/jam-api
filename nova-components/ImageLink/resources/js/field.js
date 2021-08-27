Nova.booting((Vue, router, store) => {
  Vue.component('index-image-link', require('./components/IndexField'))
  Vue.component('detail-image-link', require('./components/DetailField'))
  Vue.component('form-image-link', require('./components/FormField'))
  Vue.component('icon-external-image-link', require('./components/Icons/ExternalLink'))
})
