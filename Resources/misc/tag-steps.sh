#!/bin/bash

declare -a commits=(
    "Structure the bundle"
    "Implement the Tweet\\\Value class"
    "Tests for the Tweet\\\Type class"
    "Register the Field Type as a service"
    "SPI Integration Tests"
    "Add field view and field definition view templates"
    "Add content and edit views"
    "Tests for the validation"
    )
declare -a tags=(
    "step1_create_the_bundle"
    "step2_implement_the_tweet_value_class"
    "step3_implement_the_tweet_type_class"
    "step4_register_the_field_type_as_a_service"
    "step5_implement_the_legacy_storage_engine_converter"
    "step6_introduce_a_template"
    "step7_add_content_and_edit_views"
    "step8_add_a_validation"
    )

numberOfCommits=${#commits[@]}

for (( i=0; i<${numberOfCommits}; i++ ));
do
    git tag -d "${tags[$i]}" 2> /dev/null
    SHA1=`git log --oneline --grep "${commits[$i]}" | cut -d" " -f1`
    if [ ${SHA1} ] ; then
        git tag "${tags[$i]}" ${SHA1}
    fi
done

echo "Tags created."

read -p "Do you want to push tags to a remote repository? " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]
then
    git push --tags --force
    echo "Tags pushed."
fi
