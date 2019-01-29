<template>
    <r64-panel-item
            :hide-field="hideField"
            :field="field"
            :hide-label="hideLabelInDetail"
            :label-classes="panelLabelClasses"
            :field-classes="panelFieldClasses"
    >
        <template slot="value">
            <component v-for="(relationField, index) in field.fields" :key="index" :is="`detail-${relationField.component}`" :field="relationField" />
        </template>
    </r64-panel-item>
</template>

<script>
    import R64Field from '../../mixins/R64Field'

    export default {
        mixins: [R64Field],

        props: ['resource', 'resourceName', 'resourceId', 'field']
    }
</script>
