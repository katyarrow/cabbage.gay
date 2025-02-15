import moment from "moment";

export function useValidation() {
    const niceName = (value) => {
        return value.replaceAll('_', ' ');
    }

    let validationRules = {
        required: (value, form) => {
            if(!value) {
                return 'The :attribute is required';
            }
        },
        maxLen: (value, form, size) => {
            if(!value) {
                return;
            }
            size = parseInt(size)
            if(value.length > size) {
                return 'The :attribute is too long';
            }
        },
        date: (value, form) => {
            if(!value) {
                return;
            }
            if(!moment(value, 'YYYY-MM-DD', true).isValid()) {
                return 'The :attribute is not a valid date';
            }
        },
        time: (value, form) => {
            if(!value) {
                return;
            }
            if(!moment(value, 'HH:mm', true).isValid()) {
                return 'The :attribute is not a valid time';
            }
        },
        dateEqualOrAfter(value, form, otherDateField) {
            let otherValue = {
                today: moment().format('YYYY-MM-DD'),
                tomorrow: moment().add('day').format('YYYY-MM-DD'),
            }[otherDateField] || form[otherDateField];
            if(!value || !otherValue) {
                return;
            }
            let date1 = moment(value, 'YYYY-MM-DD', true);
            let date2 = moment(otherValue, 'YYYY-MM-DD', true);
            if(!date1.isValid() || !date2.isValid()) {
                return;
            }
            if(date1 < date2) {
                let otherAttrNiceName = 'the ' + niceName(otherDateField);
                if(['today', 'tomorrow'].includes(otherDateField)) {
                    otherAttrNiceName = otherDateField;
                }
                return 'The :attribute must be greater or equal to ' + otherAttrNiceName;
            }
        },
    };
    const validate = (rules, values) => {
        let errors = {};
        Object.entries(rules).forEach(([attribute, attributeRules]) => {
            let attributeNiceName = niceName(attribute);
            attributeRules.forEach(rule => {
                let args = [];
                if(typeof rule == 'string') {
                    let parts = rule.split(':');
                    if(parts[1]) {
                        args = parts[1].split(',');
                    }
                    rule = validationRules[parts[0]];
                }

                let message = rule(values[attribute], values, ...args);
                if(message) {
                    errors[attribute] = message.replaceAll(':attribute', attributeNiceName);
                }
            });
        });
        return errors;
    }
    return {
        validate: validate,
        rules: validationRules,
    }
}