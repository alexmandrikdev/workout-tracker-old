$(".select-all").on("change", e => {
    const tableId = getClosestTableId(e.target);
    const checkboxes = $(`#${tableId} input[type="checkbox"]`);
    checkboxes.prop("indeterminate", false);
    checkboxes.prop("checked", e.target.checked);
});

$(".day-checkbox").on("change", e => {
    const id = e.target.getAttribute("data-id");
    $(`.day-${id}-workouts`).prop("checked", e.target.checked);
});

$(".workout-checkbox").on("change", e => {
    const parentId = e.target.getAttribute("data-parent-id");
    const dayWorkouts = $(`.day-${parentId}-workouts`);
    const dayCheckbox = $(`#day-${parentId}-checkbox`);
    setCheckboxStatus(dayCheckbox, dayWorkouts);
});

$('input[type="checkbox"]:not(.select-all)').on("change", e => {
    const tableId = getClosestTableId(e.target);
    setCheckboxStatus(
        $(`#${tableId} .select-all`),
        $(`#${tableId} input[type="checkbox"]:not(.select-all)`)
    );
});

function setCheckboxStatus(target, influencers) {
    const influencersCount = influencers.length;
    const checkedInfluencersCount = influencers.filter(":checked").length;
    const propKey =
        checkedInfluencersCount === influencersCount
            ? "checked"
            : "indeterminate";
    const propValue = checkedInfluencersCount > 0;
    target.prop("checked", false);
    target.prop("indeterminate", false);
    target.prop(propKey, propValue);
    if (propKey == "indeterminate" && propValue) {
        target.prop("checked", true);
    }
}

function getClosestTableId(element) {
    return element.closest("table").id;
}
