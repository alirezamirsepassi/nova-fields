<template>
    <r64-panel-item
            :hide-field="hideField"
            :field="field"
            :hide-label="hideLabelInDetail"
            :label-classes="panelLabelClasses"
            :field-classes="panelFieldClasses"
    >
        <p slot="value" class="text-90">
            {{computedValue.label}}
        </p>
    </r64-panel-item>
</template>

<script>
    import R64Field from '../../mixins/R64Field'

    export default {
        mixins: [R64Field],

        props: ['resource', 'resourceName', 'resourceId', 'field'],

        computed: {
            computedValue() {
                return this.field.displayUsingLabels ? _.find(this.field.options, {value: this.field.value}) : this.value;
            }
        }
    }
</script>
