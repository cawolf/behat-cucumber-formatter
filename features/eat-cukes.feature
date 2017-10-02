Feature: Eat cukes Example

  Scenario: Converting the feature
    Given I have the following feature:
      """
        Feature: Eat cukes in lot

          Scenario Outline: Eating many cukes

            Given I have <X> cukes
            When I eat <Y> cukes
            Then Am I hungry? "<hungry>"

            Examples:
              | X     | Y     | hungry    |
              | 10    | 5     | false     |
              | 0     | 0     | true      |
              | 2     | 3     | true      |
              | 20600 | 20599 | false     |
    """
    When I run behat with the converter
    Then the result file will be:
      """
      [
        {
          "id": "eat-cukes-in-lot",
          "name": "Eat cukes in lot",
          "keyword": "Feature",
          "line": 1,
          "elements": [
            {
              "id": "eat-cukes-in-lot;eating-many-cukes",
              "name": "Eating many cukes",
              "keyword": "Scenario Outline",
              "line": 11,
              "steps": [
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I have 10 cukes",
                  "keyword": "Given",
                  "line": 5,
                  "match": {
                    "arguments": [
                      {
                        "val": "10"
                      }
                    ],
                    "location": "ExampleFeatureContext::passing1()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I eat 5 cukes",
                  "keyword": "When",
                  "line": 6,
                  "match": {
                    "arguments": [
                      {
                        "val": "5"
                      }
                    ],
                    "location": "ExampleFeatureContext::eat()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "Am I hungry? \"false\"",
                  "keyword": "Then",
                  "line": 7,
                  "match": {
                    "arguments": [
                      {
                        "val": "false"
                      }
                    ],
                    "location": "ExampleFeatureContext::hungry()"
                  }
                }
              ],
              "type": "scenario"
            },
            {
              "id": "eat-cukes-in-lot;eating-many-cukes",
              "name": "Eating many cukes",
              "keyword": "Scenario Outline",
              "line": 12,
              "steps": [
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I have 0 cukes",
                  "keyword": "Given",
                  "line": 5,
                  "match": {
                    "arguments": [
                      {
                        "val": "0"
                      }
                    ],
                    "location": "ExampleFeatureContext::passing1()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I eat 0 cukes",
                  "keyword": "When",
                  "line": 6,
                  "match": {
                    "arguments": [
                      {
                        "val": "0"
                      }
                    ],
                    "location": "ExampleFeatureContext::eat()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "failed",
                    "error_message": "java.lang.AssertionError: expected:\u003ctrue\u003e but was:\u003cfalse\u003e\n\tat org.junit.Assert.fail(Assert.java:88)\n\tat org.junit.Assert.failNotEquals(Assert.java:834)\n\tat org.junit.Assert.assertEquals(Assert.java:118)\n\tat org.junit.Assert.assertEquals(Assert.java:144)\n\tat com.github.cukedoctor.example.EatCukesSteps.amIHungry(EatCukesSteps.java:29)\n\tat ✽.Then Am I hungry? \"true\"(src/test/resources/features/eat-cukes.feature:7)\n"
                  },
                  "name": "Am I hungry? \"true\"",
                  "keyword": "Then",
                  "line": 7,
                  "match": {
                    "arguments": [
                      {
                        "val": "true"
                      }
                    ],
                    "location": "ExampleFeatureContext::hungry()"
                  }
                }
              ],
              "type": "scenario"
            },
            {
              "id": "eat-cukes-in-lot;eating-many-cukes",
              "name": "Eating many cukes",
              "keyword": "Scenario Outline",
              "line": 13,
              "steps": [
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I have 2 cukes",
                  "keyword": "Given",
                  "line": 5,
                  "match": {
                    "arguments": [
                      {
                        "val": "2"
                      }
                    ],
                    "location": "ExampleFeatureContext::passing1()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I eat 3 cukes",
                  "keyword": "When",
                  "line": 6,
                  "match": {
                    "arguments": [
                      {
                        "val": "3"
                      }
                    ],
                    "location": "ExampleFeatureContext::eat()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "failed",
                    "error_message": "java.lang.AssertionError: expected:\u003ctrue\u003e but was:\u003cfalse\u003e\n\tat org.junit.Assert.fail(Assert.java:88)\n\tat org.junit.Assert.failNotEquals(Assert.java:834)\n\tat org.junit.Assert.assertEquals(Assert.java:118)\n\tat org.junit.Assert.assertEquals(Assert.java:144)\n\tat com.github.cukedoctor.example.EatCukesSteps.amIHungry(EatCukesSteps.java:29)\n\tat ✽.Then Am I hungry? \"true\"(src/test/resources/features/eat-cukes.feature:7)\n"
                  },
                  "name": "Am I hungry? \"true\"",
                  "keyword": "Then",
                  "line": 7,
                  "match": {
                    "arguments": [
                      {
                        "val": "true"
                      }
                    ],
                    "location": "ExampleFeatureContext::hungry()"
                  }
                }
              ],
              "type": "scenario"
            },
            {
              "id": "eat-cukes-in-lot;eating-many-cukes",
              "name": "Eating many cukes",
              "keyword": "Scenario Outline",
              "line": 14,
              "steps": [
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I have 20600 cukes",
                  "keyword": "Given",
                  "line": 5,
                  "match": {
                    "arguments": [
                      {
                        "val": "20600"
                      }
                    ],
                    "location": "ExampleFeatureContext::passing1()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "I eat 20599 cukes",
                  "keyword": "When",
                  "line": 6,
                  "match": {
                    "arguments": [
                      {
                        "val": "20599"
                      }
                    ],
                    "location": "ExampleFeatureContext::eat()"
                  }
                },
                {
                  "result": {
                    "duration": 12345,
                    "status": "passed"
                  },
                  "name": "Am I hungry? \"false\"",
                  "keyword": "Then",
                  "line": 7,
                  "match": {
                    "arguments": [
                      {
                        "val": "false"
                      }
                    ],
                    "location": "ExampleFeatureContext::hungry()"
                  }
                }
              ],
              "type": "scenario"
            }
          ],
          "uri": "features/feature.feature",
          "description": ""
        }
      ]
      """
