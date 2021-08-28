<template>
  <div v-if="isNotObject" class="flex items-center key-value-item">
    <div class="flex flex-grow border-b border-50 key-value-fields">
      <div
        class="w-1/3 cursor-text border-l border-50"
        :class="{ 'bg-30': readOnlyKeys || !isEditable }"
      >
        <textarea
          :dusk="`key-value-key-${index}`"
          v-model="item.key"
          @focus="handleKeyFieldFocus"
          ref="keyField"
          type="text"
          class="font-mono text-sm resize-none block min-h-input w-full form-control form-input form-input-row py-4 text-90"
          :disabled="!isEditable || readOnlyKeys"
          style="background-clip: border-box"
          :class="{
            'bg-white': !isEditable || readOnlyKeys,
            'hover:bg-20 focus:bg-white': isEditable && !readOnlyKeys,
          }"
        />
      </div>

      <div @click="handleValueFieldFocus" class="w-1/3 border-l border-50">
        <textarea
          :dusk="`key-value-value-${index}`"
          v-model="item.value"
          @focus="handleValueFieldFocus"
          ref="valueField"
          type="text"
          class="font-mono text-sm block min-h-input w-full form-control form-input form-input-row py-4 text-90"
          :disabled="!isEditable"
          :class="{
            'bg-white': !isEditable,
            'hover:bg-20 focus:bg-white': isEditable,
          }"
        />
      </div>

      <div @click="handleValue2FieldFocus" class="w-1/3 border-l border-50">
        <textarea
                :dusk="`key-value-value2-${index}`"
                v-model="item.value2"
                @focus="handleValue2FieldFocus"
                ref="value2Field"
                type="text"
                class="font-mono text-sm block min-h-input w-full form-control form-input form-input-row py-4 text-90"
                :disabled="!isEditable"
                :class="{
            'bg-white': !isEditable,
            'hover:bg-20 focus:bg-white': isEditable,
          }"
        />
      </div>
    </div>

    <div
      v-if="isEditable && canDeleteRow"
      class="flex justify-center h-11 w-11 absolute"
      style="right: -50px"
    >
      <button
        @click="$emit('remove-row', item.id)"
        :dusk="`remove-key-value-${index}`"
        type="button"
        tabindex="-1"
        class="flex appearance-none cursor-pointer text-70 hover:text-primary active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline"
        title="Delete"
      >
        <icon />
      </button>
    </div>
  </div>
</template>

<script>
import autosize from 'autosize'

export default {
  props: {
    index: Number,
    item: Object,
    disabled: {
      type: Boolean,
      default: false,
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
    readOnlyKeys: {
      type: Boolean,
      default: false,
    },
    canDeleteRow: {
      type: Boolean,
      default: true,
    },
  },

  mounted() {
    autosize(this.$refs.keyField)
    autosize(this.$refs.valueField)
    autosize(this.$refs.value2Field)
  },

  methods: {
    handleKeyFieldFocus() {
      this.$refs.keyField.select()
    },

    handleValueFieldFocus() {
      this.$refs.valueField.select()
    },

    handleValue2FieldFocus() {
        this.$refs.value2Field.select()
    },
  },

  computed: {
    isNotObject() {
      return !(this.item.value instanceof Object)
    },
    isEditable() {
      return !this.readOnly && !this.disabled
    },
  },
}
</script>
