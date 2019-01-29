<template>
    <r64-default-field
            :hide-field="hideField"
            :field="field"
            :hide-label="hideLabelInForms"
            :field-classes="fieldClasses"
            :wrapper-classes="wrapperClasses"
            :label-classes="labelClasses"
    >
        <template slot="field">

            <!--<component v-for="(relationField, index) in field.fields" :key="index"-->
            <!--:is="`form-${relationField.component}`" :resource-name="relationField.baseResourceName"-->
            <!--:resource-id="relationField.resourceId" :field="relationField" :errors="errors" v-show="!value.deleted"-->
            <!--v-model="value[generateAttribute(relationField)]" @input="handleChange($event, generateAttribute(relationField))"/>-->

            <component
                    class="remove-bottom-border w-full"
                    :key="`${indexF}${f.attribute}`"
                    :ref="`${indexF}${f.attribute}`"
                    v-for="(f, indexF) in field.fields"
                    v-model="value[f.attribute]"
                    :is="`form-${f.component}`"
                    :resource-name="f.baseResourceName"
                    :resource-id="f.resourceId"
                    :field="f"
                    :base-classes="field.childConfig"
                    :errors="errors"
                    :data-set="dataSets[indexF + '-' + f.component]"
                    @data-set-available="data => dataSets[indexF + '-' + f.component] = data"
                    @input="handleChange($event, f.attribute)"
            ></component>

            <button type="button" v-if="field.hasOwnProperty('show-delete-button')"
                    class="btn btn-default btn-primary inline-flex items-center relative float-left my-2"
                    @click="resetFields">{{value.deleted ? __('Restore'): __('Delete')}}
            </button>
            <p v-if="hasError" class="my-2 text-danger" v-html="firstError"/>
        </template>
    </r64-default-field>
</template>

<script>
    import {Errors, FormField, HandlesValidationErrors} from 'laravel-nova'
    import R64Field from '../../mixins/R64Field'

    export default {
        mixins: [HandlesValidationErrors, FormField, R64Field],

        props: ['resourceName', 'resourceId', 'field'],

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || {}
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, JSON.stringify(this.value) || {})
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value, attribute) {
                console.log(`${attribute} changed to: ${value}`);
                this.value[attribute] = value
            },

            /**
             * Deleting all fields
             */
            resetFields() {
                this.value.deleted = !this.value.deleted;
            },
        },

        mounted() {
            _.forOwn(this.field.fields, (field) => {
                if (!this.value.hasOwnProperty(field.attribute)) {
                    this.$set(this.value, field.attribute, null)
                }
            });
            this.$set(this.value, 'deleted', false);
            window.myVue = this;
        },

        data() {
            return {
                dataSets: {}
            }
        }
    }
</script>
