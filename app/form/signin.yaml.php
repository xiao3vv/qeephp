
user_name:
  _ui: textbox
  _label: "Mail:"
  _filters: "trim"
  _validations:
    - ["not_empty", "ID ?"]
  class: 'ipt'
  placeholder: "Authorized ID"

user_pass:
  _ui: password
  _label: "Pass:"
  _filters: "trim"
  _validations:
    - ["not_empty", "Password ?"]
  class: 'ipt'
  autocomplete: 'off'
  placeholder: "Password"
