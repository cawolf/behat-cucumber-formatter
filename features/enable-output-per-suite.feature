Feature: Calculator example

  Scenario: Default to only output a single result file
    Given I have the following feature:
      """
      Feature: Calculator

      Scenario: Adding numbers
      Given I have numbers 1 and 2
      When I sum the numbers
      Then I should have 3 as result
      """
    And I have the following feature file "eat-cukes.feature" stored in "otherfeatures":
      """
      Feature: Eat cukes in lot

      Scenario: Eating many cukes
      Given I have 10 cukes
      When I eat 5 cukes
      Then Am I hungry? false
      """
    When I run behat with the converter
    Then 1 result file should be generated

  Scenario: Output to a result file per suite
    Given I have the enabled the "resultFilePerSuite" option
    And I have the following feature:
      """
      Feature: Calculator

      Scenario: Adding numbers
      Given I have numbers 1 and 2
      When I sum the numbers
      Then I should have 3 as result
      """
    And I have the following feature file "eat-cukes.feature" stored in "otherfeatures":
      """
      Feature: Eat cukes in lot

      Scenario: Eating many cukes
      Given I have 10 cukes
      When I eat 5 cukes
      Then Am I hungry? false
      """
    When I run behat with the converter
    Then 2 result files should be generated
