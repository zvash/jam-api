<template>
  <panel-item :field="field">
    <template slot="value">
      <KeyValueTable
        v-if="theData.length > 0"
        :edit-mode="false"
        class="overflow-hidden"
      >
        <KeyValueHeader
          :key-label="field.keyLabel"
          :value-label="field.valueLabel"
          :value2-label="field.value2Label"
        />

        <div class="bg-white overflow-hidden key-value-items">
          <KeyValueItem
            v-for="item in theData"
            :item="item"
            :disabled="true"
            :key="item.key"
          />
        </div>
      </KeyValueTable>
    </template>
  </panel-item>
</template>

<script>
import KeyValueItem from './KeyTwoValuesField/KeyValueItem'
import KeyValueHeader from './KeyTwoValuesField/KeyValueHeader'
import KeyValueTable from './KeyTwoValuesField/KeyValueTable'

export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],

  components: { KeyValueTable, KeyValueHeader, KeyValueItem },

  data: () => ({ theData: [] }),

  created() {
    this.theData = _.map(this.field.value || {}, (value, key) => ({
      key,
      value: value[0],
      value2: value[1],
    }))
  },
}
</script>
