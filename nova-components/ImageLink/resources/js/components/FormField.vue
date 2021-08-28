<template>
  <div class="flex border-b border-40">
    <div class="w-1/5 py-4">
      <!--<slot>-->
      <h4 class="px-8 font-normal text-80">
        {{ field.name }}
      </h4>
      <!--</slot>-->
    </div>
    <div class="px-8 py-4 w-4/5">
      <!--<slot name="value">-->
      <p v-if="field.value" class="text-90">
        <a :target="field.blank ? field.blank : '_self'" :class="field.classes ? field.classes : defaultClasses"
           :href="field.href">
          {{ field.text ? field.text : field.value }}

        </a>
      </p>
      <p v-else>&mdash;</p>
      <!--</slot>-->
    </div>
  </div>
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [HandlesValidationErrors, FormField],

        props: ['resource', 'resourceName', 'resourceId', 'field'],

        data: () => ({
            defaultClasses: [
                'no-underline',
                'dim',
                'text-primary',
                'font-bold'
            ]
        }),
        methods: {
            /**
             * Provide a function that fills a passed FormData object with the
             * field's internal value attribute.
             */
            fill(formData) {
                // formData.append(this.field.attribute, this.value || '')
            },
        },
    }
</script>

