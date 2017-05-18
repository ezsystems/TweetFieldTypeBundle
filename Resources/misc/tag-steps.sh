#!/bin/bash

declare -a commits=(
	"Create the bundle"
	"Structure the bundle"
	"Implement the Tweet\Value class"
	"Implement the Tweet\Type class"
	"Register the Field Type as a service"
	"Implement the Legacy Storage Engine Converter"
	"Add field view and field definition view templates"
	)
declare -a tags=(
	"step1_create_the_bundle"
	"step2_structure_the_bundle"
	"step3_implement_the_tweet_value_class"
	"step4_implement_the_tweet_type_class"
	"step5_register_the_field_type_as_a_service"
	"step6_implement_the_legacy_storage_engine_converter"
	"step7_add_field_view_and_field_definition_view_templates"
	)

numberOfCommits=${#commits[@]}

for (( i=0; i<${numberOfCommits}; i++ ));
do
	SHA1=`git log --oneline --all --grep "${commits[$i]}" | cut -d" " -f1`
	git tag "${tags[$i]}" ${SHA1}
done

git push --tags

echo "Tags created and pushed."
