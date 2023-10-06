export const MeanAgePopup = ({ feature }) => (
    <div>
        <h3>Mean Age</h3>
        <p>{feature.properties.value}</p>
    </div>
);

export const MedianAgePopup = ({ feature }) => (
    <div>
        <h3>Median Age</h3>
        <p>{feature.properties.value}</p>
    </div>
);

export const popupMapping = {
    "mean-age": MeanAgePopup,
    "median-age": MedianAgePopup
};
