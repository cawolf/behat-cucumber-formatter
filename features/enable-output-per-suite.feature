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
    And I have the following feature file "eat-cukes.feature" stored in "othersuite":
      """
      Feature: Eat cukes in lot

      Scenario: Eating many cukes
      Given I have 10 cukes
      When I eat 5 cukes
      Then Am I hungry? false
      """
    When I run behat with the converter and no specific suite is specified
    Then 1 result file should be generated
    And there should be 2 features in the report "report-all.json"

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
    And I have the following feature file "eat-cukes.feature" stored in "othersuite":
      """
      Feature: Eat cukes in lot

      Scenario: Eating many cukes
      Given I have 10 cukes
      When I eat 5 cukes
      Then Am I hungry? false
      """
    When I run behat with the converter and no specific suite is specified
    Then 2 result files should be generated
    And there should be 1 feature in the report "report-default.json"
    And there should be 1 feature in the report "report-othersuite.json"
