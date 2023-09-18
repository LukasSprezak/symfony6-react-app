import React, {useEffect} from "react";
import { NavigateFunction, useNavigate } from "react-router-dom";
import axios from "axios";

const Product: React.FC = () => {
    const navigate: NavigateFunction = useNavigate();
    const defaultProducts:Product[] = [];
    const [products, setProducts]: [Product[], (products: Product[]) => void] = React.useState(defaultProducts);
    const [error, setError]: [string, (error: string) => void] = React.useState('');

    useEffect(() => {
        axios
            .get<Product[]|any>('/api/products')
            .then(response => {
                setProducts(response.data['hydra:member']);
            })
            .catch(exception => {
                const error =
                    exception.response.status === 404
                        ? "Resource Not found"
                        : "An unexpected error has occurred";
                setError(error);
            });
    }, []);

    return (
        <div className="App">
            <ul className="products">
                {products.map((product: Product) => (
                    <div key={product.id}>
                        <p>{product.name}</p>
                        <h3>{product.description}</h3>
                    </div>
                ))}
            </ul>
            {error && <p className="error">{error}</p>}
            <button className="btn" onClick={() => navigate(-1)}>
                Go Back
            </button>
        </div>
    );
}

export default Product;