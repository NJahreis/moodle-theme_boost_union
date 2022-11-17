@theme @theme_boost_union @theme_boost_union_flavourssettings
Feature: Configuring the theme_boost_union plugin on the "Flavours" page
  In order to use the features
  As admin
  I need to be able to configure the theme Boost Union plugin

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |
      | teacher2 |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
      | Course 2 | C2        | CAT2     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C2     | editingteacher |

  Scenario: Flavours: Management - When the theme is installed, no flavours exist
  # TODO

  Scenario: Flavours: Management - Preview existing flavours
  # TODO

  Scenario: Flavours: Management - Edit existing flavours
  # TODO

  Scenario: Flavours: Management - Delete existing flavours (until none is left anymore)
  # TODO

  Scenario: Flavours: Management - Move existing flavours up and down
  # TODO

  Scenario: Flavours: Management - Set title and description (and show them in the overview table)
  # TODO

  Scenario: Flavours: Favicon - Upload a favicon (with a global favicon not having been uploaded before)
  # TODO

  Scenario: Flavours: Favicon - Upload a favicon (with a global favicon being overridden)
  # TODO

  Scenario: Flavours: Logo - Upload a logo (with a global logo not having been uploaded before)
  # TODO

  Scenario: Flavours: Logo - Upload a logo (with a global logo being overridden)
  # TODO

  Scenario: Flavours: Compact logo - Upload a compact logo (with a global compact logo not having been uploaded before)
  # TODO

  Scenario: Flavours: Compact logo - Upload a compact logo (with a global compact logo being overridden)
  # TODO

  Scenario: Flavours: Background image - Upload a Background image (with a global background image not having been uploaded before)
  # TODO

  Scenario: Flavours: Background image - Upload a Background image (with a global background image being overridden)
  # TODO

  Scenario: Flavours: Raw SCSS - Add raw scss to the page
  # TODO

  Scenario: Flavours: Application - Apply a flavour to a category without subcategories (and show this fact in the overview table)
  # TODO

  Scenario: Flavours: Application - Apply a flavour to a category with subcategories (and show this fact in the overview table)
  # TODO

  Scenario: Flavours: Application - Apply a flavour to a cohort (and show this fact in the overview table)
  # TODO

  Scenario: Flavours: Application - Do not apply a flavour to a category (countercheck if no category was selected OR the select box was changed to NO)
  # TODO

  Scenario: Flavours: Application - Do not apply a flavour to a cohort (countercheck if no cohort was selected OR the select box was changed to NO)
  # TODO

  Scenario: Flavours: Application - Stop after the first matching flavour
  # TODO

  Scenario: Flavours: Flavour ID is added as body attribute
  # TODO
