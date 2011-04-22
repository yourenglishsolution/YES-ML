// ========================= Copyright Epistema 2002 ========================
//
// ResetAllCombos.js
//
//
// ==========================================================================

// resetAllCombos
// --------------
// This function makes sure all combos are set in such a way no
// two combos show the same value :
// It's called in the onclick handler of the selects

var bResetingCombos = false;

function resetAllCombos(callingCombo)
{
	if (bResetingCombos)
		return;

	var CallingComboIndex = callingCombo.selectedIndex;

	if (CallingComboIndex < 1)
		return;

	bResetingCombos = true;

	var i, j, k;

	var combos = callingCombo.form.getElementsByTagName("SELECT");

	for(i = 0; i < combos.length; i++)
	{
		var aRow = combos[i];

		if (aRow.name == 'NavigationSelect')
			continue;

		if (aRow.selectedIndex == CallingComboIndex &&
				aRow != callingCombo)
			aRow.selectedIndex = 0;
	}

	bResetingCombos = false;
}
